<?php

class Report extends Model
{
  // Get transactions with filter (date range and product)
  public function getTransactionsWithFilter($filter)
  {
    $sql = "SELECT t.id, t.total_amount, t.created_at, u.name as cashier_name,
                   GROUP_CONCAT(CONCAT(p.name, ' (', td.qty, 'x)') SEPARATOR ', ') as products
            FROM transactions t
            JOIN users u ON t.user_id = u.id
            LEFT JOIN transaction_details td ON t.id = td.transaction_id
            LEFT JOIN products p ON td.product_id = p.id
            WHERE DATE(t.created_at) BETWEEN ? AND ?";

    $params = [$filter['start_date'], $filter['end_date']];

    if (!empty($filter['product_id'])) {
      $sql .= " AND td.product_id = ?";
      $params[] = $filter['product_id'];
    }

    $sql .= " GROUP BY t.id ORDER BY t.created_at DESC";

    $stmt = $this->db->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  // Get sales summary
  public function getSalesSummary($filter)
  {
    $sql = "SELECT 
              COUNT(DISTINCT t.id) as total_transactions,
              COALESCE(SUM(t.total_amount), 0) as total_revenue,
              COALESCE(SUM(td.qty), 0) as total_items
            FROM transactions t
            LEFT JOIN transaction_details td ON t.id = td.transaction_id
            WHERE DATE(t.created_at) BETWEEN ? AND ?";

    $params = [$filter['start_date'], $filter['end_date']];

    if (!empty($filter['product_id'])) {
      $sql .= " AND td.product_id = ?";
      $params[] = $filter['product_id'];
    }

    $stmt = $this->db->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  // Get all products for filter dropdown
  public function getAllProducts()
  {
    $stmt = $this->db->query("SELECT id, name FROM products ORDER BY name");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  // Statistics for dashboard - Today
  public function getTodayStats()
  {
    $sql = "SELECT 
              COUNT(DISTINCT id) as transactions,
              COALESCE(SUM(total_amount), 0) as revenue
            FROM transactions 
            WHERE DATE(created_at) = CURDATE()";

    $stmt = $this->db->query($sql);
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  // Statistics for dashboard - This Month
  public function getMonthStats()
  {
    $sql = "SELECT 
              COUNT(DISTINCT id) as transactions,
              COALESCE(SUM(total_amount), 0) as revenue
            FROM transactions 
            WHERE MONTH(created_at) = MONTH(CURDATE()) 
            AND YEAR(created_at) = YEAR(CURDATE())";

    $stmt = $this->db->query($sql);
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  // Statistics for dashboard - This Year
  public function getYearStats()
  {
    $sql = "SELECT 
              COUNT(DISTINCT id) as transactions,
              COALESCE(SUM(total_amount), 0) as revenue
            FROM transactions 
            WHERE YEAR(created_at) = YEAR(CURDATE())";

    $stmt = $this->db->query($sql);
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  // Get top selling products
  public function getTopProducts($limit = 5)
  {
    $sql = "SELECT p.name, p.image,
              SUM(td.qty) as total_sold,
              SUM(td.subtotal) as total_revenue
            FROM transaction_details td
            JOIN products p ON td.product_id = p.id
            GROUP BY td.product_id
            ORDER BY total_sold DESC
            LIMIT ?";

    $stmt = $this->db->prepare($sql);
    $stmt->execute([$limit]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  // Get recent transactions
  public function getRecentTransactions($limit = 10)
  {
    $sql = "SELECT t.id, t.total_amount, t.created_at, u.name as cashier_name
            FROM transactions t
            JOIN users u ON t.user_id = u.id
            ORDER BY t.created_at DESC
            LIMIT ?";

    $stmt = $this->db->prepare($sql);
    $stmt->execute([$limit]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  // Get monthly sales data for chart
  public function getMonthlyChartData()
  {
    $sql = "SELECT 
              DATE_FORMAT(created_at, '%Y-%m') as month,
              COUNT(id) as transactions,
              SUM(total_amount) as revenue
            FROM transactions
            WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
            GROUP BY DATE_FORMAT(created_at, '%Y-%m')
            ORDER BY month ASC";

    $stmt = $this->db->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  // Get daily sales data for chart (last 7 days)
  public function getDailyChartData()
  {
    $sql = "SELECT 
              DATE(created_at) as date,
              COUNT(id) as transactions,
              COALESCE(SUM(total_amount), 0) as revenue
            FROM transactions
            WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
            GROUP BY DATE(created_at)
            ORDER BY date ASC";

    $stmt = $this->db->query($sql);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fill missing dates with zero values
    $data = [];
    for ($i = 6; $i >= 0; $i--) {
      $date = date('Y-m-d', strtotime("-$i days"));
      $found = false;

      foreach ($results as $row) {
        if ($row['date'] === $date) {
          $data[] = $row;
          $found = true;
          break;
        }
      }

      if (!$found) {
        $data[] = [
          'date' => $date,
          'transactions' => 0,
          'revenue' => 0
        ];
      }
    }

    return $data;
  }
}

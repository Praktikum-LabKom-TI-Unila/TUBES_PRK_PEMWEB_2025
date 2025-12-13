<?php

function getProductImageUrl($imageName)
{
    if (empty($imageName) || $imageName === 'default.jpg') {
        return null; 
    }
    
    $imagePath = BASE_PATH . "/src/public/uploads/products/" . $imageName;
    
    if (file_exists($imagePath)) {
        return BASE_URL . "/public/uploads/products/" . $imageName;
    }
    
    return null; 
}

function formatRupiah($amount)
{
    return 'Rp ' . number_format($amount, 0, ',', '.');
}
-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema mydb
-- -----------------------------------------------------
-- -----------------------------------------------------
-- Schema sipinda
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema sipinda
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `sipinda` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci ;
USE `sipinda` ;

-- -----------------------------------------------------
-- Table `sipinda`.`users`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sipinda`.`users` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `full_name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `phone` VARCHAR(50) NULL DEFAULT NULL,
  `nik` VARCHAR(20) NULL DEFAULT NULL,
  `address` TEXT NULL DEFAULT NULL,
  `profile_photo` VARCHAR(255) NULL DEFAULT NULL,
  `role` ENUM('warga', 'admin', 'petugas') NOT NULL DEFAULT 'warga',
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `email` (`email` ASC) VISIBLE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `sipinda`.`auth_tokens`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sipinda`.`auth_tokens` (
  `id` CHAR(36) NOT NULL,
  `user_id` INT NOT NULL,
  `token` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `expires_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `token_unique` (`token` ASC) VISIBLE,
  INDEX `fk_auth_tokens_user` (`user_id` ASC) VISIBLE,
  CONSTRAINT `fk_auth_tokens_user`
    FOREIGN KEY (`user_id`)
    REFERENCES `sipinda`.`users` (`id`)
    ON DELETE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `sipinda`.`officers`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sipinda`.`officers` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `employee_id` VARCHAR(100) NOT NULL,
  `department` VARCHAR(255) NOT NULL,
  `specialization` ENUM('Jalan_Raya', 'Penerangan_Jalan', 'Drainase', 'Trotoar', 'Taman', 'Jembatan', 'Rambu_Lalu_Lintas', 'Fasilitas_Umum', 'Lainnya') NOT NULL,
  `officer_status` ENUM('tersedia', 'sibuk') NOT NULL DEFAULT 'tersedia',
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_officers_user` (`user_id` ASC) VISIBLE,
  CONSTRAINT `fk_officers_user`
    FOREIGN KEY (`user_id`)
    REFERENCES `sipinda`.`users` (`id`)
    ON DELETE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `sipinda`.`complaints`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sipinda`.`complaints` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `reporter_id` INT NOT NULL,
  `category` ENUM('Jalan_Raya', 'Penerangan_Jalan', 'Drainase', 'Trotoar', 'Taman', 'Jembatan', 'Rambu_Lalu_Lintas', 'Fasilitas_Umum', 'Lainnya') NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `description` TEXT NULL DEFAULT NULL,
  `photo_before` VARCHAR(255) NULL DEFAULT NULL,
  `latitude` DOUBLE NULL DEFAULT NULL,
  `longitude` DOUBLE NULL DEFAULT NULL,
  `address` VARCHAR(255) NULL DEFAULT NULL,
  `status` ENUM('diajukan', 'diverifikasi_admin', 'ditugaskan_ke_petugas', 'dalam_proses', 'menunggu_validasi_admin', 'ditolak_admin', 'selesai') NOT NULL DEFAULT 'diajukan',
  `assigned_officer_id` INT NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_complaints_reporter` (`reporter_id` ASC) VISIBLE,
  INDEX `fk_complaints_officer` (`assigned_officer_id` ASC) VISIBLE,
  CONSTRAINT `fk_complaints_officer`
    FOREIGN KEY (`assigned_officer_id`)
    REFERENCES `sipinda`.`officers` (`id`)
    ON DELETE SET NULL,
  CONSTRAINT `fk_complaints_reporter`
    FOREIGN KEY (`reporter_id`)
    REFERENCES `sipinda`.`users` (`id`)
    ON DELETE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `sipinda`.`complaint_progress`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sipinda`.`complaint_progress` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `complaint_id` INT NOT NULL,
  `status` ENUM('diajukan', 'diverifikasi_admin', 'ditugaskan_ke_petugas', 'dalam_proses', 'menunggu_validasi_admin', 'ditolak_admin', 'selesai') NOT NULL,
  `note` TEXT NULL DEFAULT NULL,
  `created_by` INT NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_progress_complaint` (`complaint_id` ASC) VISIBLE,
  INDEX `fk_progress_user` (`created_by` ASC) VISIBLE,
  CONSTRAINT `fk_progress_complaint`
    FOREIGN KEY (`complaint_id`)
    REFERENCES `sipinda`.`complaints` (`id`)
    ON DELETE CASCADE,
  CONSTRAINT `fk_progress_user`
    FOREIGN KEY (`created_by`)
    REFERENCES `sipinda`.`users` (`id`)
    ON DELETE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `sipinda`.`completion_proofs`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sipinda`.`completion_proofs` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `complaint_id` INT NOT NULL,
  `officer_id` INT NOT NULL,
  `photo_after` VARCHAR(255) NULL DEFAULT NULL,
  `notes` TEXT NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_proof_complaint` (`complaint_id` ASC) VISIBLE,
  INDEX `fk_proof_officer` (`officer_id` ASC) VISIBLE,
  CONSTRAINT `fk_proof_complaint`
    FOREIGN KEY (`complaint_id`)
    REFERENCES `sipinda`.`complaints` (`id`)
    ON DELETE CASCADE,
  CONSTRAINT `fk_proof_officer`
    FOREIGN KEY (`officer_id`)
    REFERENCES `sipinda`.`officers` (`id`)
    ON DELETE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `sipinda`.`officer_tasks`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sipinda`.`officer_tasks` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `officer_id` INT NOT NULL,
  `complaint_id` INT NOT NULL,
  `started_at` TIMESTAMP NULL DEFAULT NULL,
  `finished_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_tasks_officer` (`officer_id` ASC) VISIBLE,
  INDEX `fk_tasks_complaint` (`complaint_id` ASC) VISIBLE,
  CONSTRAINT `fk_tasks_complaint`
    FOREIGN KEY (`complaint_id`)
    REFERENCES `sipinda`.`complaints` (`id`)
    ON DELETE CASCADE,
  CONSTRAINT `fk_tasks_officer`
    FOREIGN KEY (`officer_id`)
    REFERENCES `sipinda`.`officers` (`id`)
    ON DELETE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

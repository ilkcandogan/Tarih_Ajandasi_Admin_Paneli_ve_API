SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
--

DELIMITER $$
--
-- Yordamlar
--
CREATE DEFINER=`root`@`%` PROCEDURE `sp_ADMIN_ACCOUNT_RESET_PASSWORD` (IN `p_USERNAME` VARCHAR(20) charset utf8, IN `p_PASSWORD` CHAR(32) charset utf8, IN `p_ACCOUNT_USERNAME` VARCHAR(20) charset utf8)  BEGIN
	SET @ERROR = 0;
    SET @ADMIN_USERNAME = (SELECT USERNAME FROM tb_admin WHERE USERNAME = p_USERNAME and PASSWORD = p_PASSWORD);
    IF (@ADMIN_USERNAME = 'admin') THEN
		IF NOT (p_ACCOUNT_USERNAME = 'admin') THEN
            SET @ACCOUNT_ID = (SELECT ID FROM tb_admin WHERE USERNAME = p_ACCOUNT_USERNAME);
            IF (@ACCOUNT_ID > 0) THEN
                UPDATE tb_admin SET PASSWORD = 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx' WHERE ID = @ACCOUNT_ID;
			ELSE
				SET @ERROR = 3; -- Not Found Username!
            END IF;
        ELSE
			SET @ERROR = 2; -- Invalid Action For Admin!
        END IF;
    ELSE
		SET @ERROR = 1; -- Access Denied (Only Allowed For Admin!)
    END IF;
    SELECT @ERROR;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `sp_ADMIN_ACTIVATION_ACCOUNT` (IN `p_USERNAME` VARCHAR(20) charset utf8, IN `p_PASSWORD` CHAR(32) charset utf8, IN `p_ACCOUNT_USERNAME` VARCHAR(20) charset utf8)  BEGIN
	SET @ERROR = 0;
    SET @ADMIN_USERNAME = (SELECT USERNAME FROM tb_admin WHERE USERNAME = p_USERNAME and PASSWORD = p_PASSWORD);
    IF (@ADMIN_USERNAME = 'admin') THEN
		IF NOT (p_ACCOUNT_USERNAME = 'admin') THEN
            SET @ACCOUNT_ID = (SELECT ID FROM tb_admin WHERE USERNAME = p_ACCOUNT_USERNAME);
            IF (@ACCOUNT_ID > 0) THEN
				UPDATE tb_admin SET INACTIVE_ACCOUNT = false WHERE ID = @ACCOUNT_ID;
			ELSE
				SET @ERROR = 3; -- Not Found Username!
            END IF;
        ELSE
			SET @ERROR = 2; -- Invalid Action For Admin!
        END IF;
    ELSE
		SET @ERROR = 1; -- Access Denied (Only Allowed For Admin!)
    END IF;
    SELECT @ERROR;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `sp_ADMIN_ADD_ACCOUNT` (IN `p_USERNAME` VARCHAR(20) charset utf8, IN `p_PASSWORD` CHAR(32) charset utf8, IN `p_NEW_FIRST_NAME` VARCHAR(100) charset utf8, IN `p_NEW_LAST_NAME` VARCHAR(100) charset utf8, IN `p_NEW_USERNAME` VARCHAR(20) charset utf8, IN `p_NEW_EMAIL` VARCHAR(200) charset utf8, IN `p_NEW_PHONE_NUMBER` CHAR(11) charset utf8, IN `p_NEW_VERIFY_CODE` CHAR(6) charset utf8, IN `p_NEW_PASSWORD` CHAR(32) charset utf8)  BEGIN
	SET @ERROR = 0;
    SET @ADMIN_ID = (SELECT ID FROM tb_admin WHERE USERNAME = p_USERNAME and PASSWORD = p_PASSWORD);
    
    IF NOT EXISTS (SELECT ID FROM tb_admin WHERE USERNAME = p_NEW_USERNAME) THEN
		IF NOT EXISTS (SELECT ID FROM tb_admin WHERE EMAIL = p_NEW_EMAIL) THEN
			IF (p_NEW_PHONE_NUMBER = '') THEN
				SET p_NEW_PHONE_NUMBER = null; 
            END IF;
            
            INSERT INTO tb_admin(
				AUTH_ADMIN_ID,
				FIRST_NAME,
				LAST_NAME,
				USERNAME,
				EMAIL,
				PHONE_NUMBER,
				VERIFY_CODE,
				PASSWORD,
				LAST_LOGIN_IP_ADDRESS
			) 
			VALUES (
				@ADMIN_ID,
				p_NEW_FIRST_NAME,
				p_NEW_LAST_NAME,
				p_NEW_USERNAME,
				p_NEW_EMAIL,
				p_NEW_PHONE_NUMBER,
				null, -- p_NEW_VERIFY_CODE (Test is null data)
				p_NEW_PASSWORD,
				'0.0.0.0'
			);
        ELSE
			SET @ERROR = 2; -- Email already exists!
        END IF;
    ELSE
		SET @ERROR = 1; -- Username already exists!
    END IF;
    SELECT @ERROR;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `sp_ADMIN_ALL_ACCOUNT_LIST` ()  BEGIN
	SELECT ID as ADMIN_ID ,FIRST_NAME,LAST_NAME, USERNAME, EMAIL, PHONE_NUMBER, date_format(REG_DATE, '%d.%m.%Y %H:%i:%s') AS REG_DATE , LAST_LOGIN_IP_ADDRESS FROM tb_admin WHERE INACTIVE_ACCOUNT = 0;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `sp_ADMIN_ALL_MEMBERS` ()  BEGIN
	SELECT ID AS MEMBER_ID, FIRST_NAME, LAST_NAME, EMAIL, ONESIGNAL_PLAYER_ID , IP_ADDRESS, date_format(REG_DATE, '%d.%m.%Y %H:%i:%s') AS REG_DATE FROM tb_members WHERE DELETED_ACCOUNT = 0 ORDER BY ID DESC;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `sp_ADMIN_CATEGORY_FILTER` (IN `p_CATEGORY_ID` INT UNSIGNED, IN `p_SUBCATEGORY_ID` INT UNSIGNED)  BEGIN
	SELECT * FROM tb_files WHERE CATEGORY_ID = p_CATEGORY_ID and SUBCATEGORY_ID = p_SUBCATEGORY_ID;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `sp_ADMIN_DELETE_ACCOUNT` (IN `p_USERNAME` VARCHAR(20) charset utf8, IN `p_PASSWORD` CHAR(32) charset utf8, IN `p_ACCOUNT_USERNAME` VARCHAR(20) charset utf8)  BEGIN
	SET @ERROR = 0;
    SET @ADMIN_USERNAME = (SELECT USERNAME FROM tb_admin WHERE USERNAME = p_USERNAME and PASSWORD = p_PASSWORD);
    IF (@ADMIN_USERNAME = 'admin') THEN
		IF NOT (p_ACCOUNT_USERNAME = 'admin') THEN
            SET @ACCOUNT_ID = (SELECT ID FROM tb_admin WHERE USERNAME = p_ACCOUNT_USERNAME);
            IF (@ACCOUNT_ID > 0) THEN
                DELETE FROM tb_admin WHERE ID = @ACCOUNT_ID;
			ELSE
				SET @ERROR = 3; -- Not Found Username!
            END IF;
        ELSE
			SET @ERROR = 2; -- Invalid Action For Admin!
        END IF;
    ELSE
		SET @ERROR = 1; -- Access Denied (Only Allowed For Admin!)
    END IF;
    SELECT @ERROR;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `sp_ADMIN_FILE_CATEGORY_ADD` (IN `p_USERNAME` VARCHAR(20) charset utf8, IN `p_PASSWORD` CHAR(32) charset utf8, IN `p_CATEGORY_NAME` VARCHAR(100) charset utf8)  BEGIN
	SET @ERROR = 0;
    SET @ADMIN_ID = (SELECT ID FROM tb_admin WHERE USERNAME = p_USERNAME and PASSWORD = p_PASSWORD);
    
    IF (@ADMIN_ID > 0) THEN
		IF NOT EXISTS (SELECT ID FROM tb_file_category WHERE CATEGORY_NAME = p_CATEGORY_NAME) THEN
			INSERT INTO tb_file_category (
				CATEGORY_NAME
            ) VALUES (
				p_CATEGORY_NAME
            );
        ELSE
			SET @ERROR = 2; -- Already exists category name!
        END IF;
    ELSE
		SET @ERROR = 1; -- Access Denied!
    END IF;
    SELECT @ERROR;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `sp_ADMIN_FILE_CATEGORY_DELETE` (IN `p_USERNAME` VARCHAR(20) charset utf8, IN `p_PASSWORD` CHAR(32) charset utf8, IN `p_CATEGORY_ID` INT UNSIGNED)  BEGIN
	SET @ERROR = 0;
    SET @ADMIN_ID = (SELECT ID FROM tb_admin WHERE USERNAME = p_USERNAME and PASSWORD = p_PASSWORD);
    
    IF (@ADMIN_ID > 0) THEN
		DELETE FROM tb_file_category WHERE ID = p_CATEGORY_ID; -- category table delete
        DELETE FROM tb_file_subcategory WHERE CATEGORY_ID = p_CATEGORY_ID; -- subcategory table delete
		
        SELECT FILE_NO FROM tb_files WHERE CATEGORY_ID = p_CATEGORY_ID;
        DELETE FROM tb_files WHERE CATEGORY_ID = p_CATEGORY_ID;
    ELSE
		SET @ERROR = 1; -- Access Denied!
    END IF;
    -- SELECT @ERROR;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `sp_ADMIN_FILE_CATEGORY_LIST` (IN `p_USERNAME` VARCHAR(20) charset utf8, IN `p_PASSWORD` CHAR(32) charset utf8)  BEGIN
	SET @ERROR = 0;
    SET @ADMIN_ID = (SELECT ID FROM tb_admin WHERE USERNAME = p_USERNAME and PASSWORD = p_PASSWORD);
    
    IF (@ADMIN_ID > 0) THEN
		SELECT * from tb_file_category;
    ELSE
		SET @ERROR = 1; -- Access Denied!
    END IF;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `sp_ADMIN_FILE_CATEGORY_NAME` (IN `p_CATEGORY_ID` INT UNSIGNED)  BEGIN
	SELECT CATEGORY_NAME FROM tb_file_category WHERE ID = p_CATEGORY_ID;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `sp_ADMIN_FILE_DELETE` (IN `p_USERNAME` VARCHAR(20) charset utf8, IN `p_PASSWORD` CHAR(32) charset utf8, IN `p_FILE_NO` VARCHAR(20) charset utf8)  BEGIN
	SET @ERROR = 0;
    SET @ADMIN_ID = (SELECT ID FROM tb_admin WHERE USERNAME = p_USERNAME and PASSWORD = p_PASSWORD);
    
    IF (@ADMIN_ID > 0) THEN
		DELETE FROM tb_files WHERE FILE_NO = p_FILE_NO;
    ELSE
		SET @ERROR = 1; -- Access Denied!
    END IF;
    SELECT @ERROR;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `sp_ADMIN_FILE_LIST` ()  BEGIN
	SELECT ID,FILE_NAME,FILE_SIZE,FILE_DESCRIPTION,FILE_NO,TOTAL_DOWNLOAD ,date_format(REG_DATE, '%d.%m.%Y %H:%i:%s') AS REG_DATE FROM tb_files ORDER BY ID DESC;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `sp_ADMIN_FILE_SUBCATEGORY_ADD` (IN `p_USERNAME` VARCHAR(20) charset utf8, IN `p_PASSWORD` CHAR(32) charset utf8, IN `p_CATEGORY_ID` INT UNSIGNED, IN `p_SUBCATEGORY_NAME` VARCHAR(100) charset utf8)  BEGIN
	SET @ERROR = 0;
    SET @ADMIN_ID = (SELECT ID FROM tb_admin WHERE USERNAME = p_USERNAME and PASSWORD = p_PASSWORD);
    
    IF (@ADMIN_ID > 0) THEN
		IF NOT EXISTS (SELECT ID FROM tb_file_subcategory WHERE CATEGORY_ID = p_CATEGORY_ID and SUBCATEGORY_NAME = p_SUBCATEGORY_NAME) THEN
			INSERT INTO tb_file_subcategory (
				CATEGORY_ID,
				SUBCATEGORY_NAME
            ) VALUES (
				p_CATEGORY_ID,
                p_SUBCATEGORY_NAME
            );
        ELSE
			SET @ERROR = 2; -- Already exists category name!
        END IF;
    ELSE
		SET @ERROR = 1; -- Access Denied!
    END IF;
    SELECT @ERROR;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `sp_ADMIN_FILE_SUBCATEGORY_DELETE` (IN `p_USERNAME` VARCHAR(20) charset utf8, IN `p_PASSWORD` CHAR(32) charset utf8, IN `p_CATEGORY_ID` INT UNSIGNED, IN `p_SUBCATEGORY_ID` INT UNSIGNED)  BEGIN
	SET @ERROR = 0;
    SET @ADMIN_ID = (SELECT ID FROM tb_admin WHERE USERNAME = p_USERNAME and PASSWORD = p_PASSWORD);
    
    IF (@ADMIN_ID > 0) THEN
        DELETE FROM tb_file_subcategory WHERE ID = p_SUBCATEGORY_ID; -- subcategory table delete
        
        SELECT FILE_NO FROM tb_files WHERE SUBCATEGORY_ID = p_SUBCATEGORY_ID and CATEGORY_ID = p_CATEGORY_ID;
        DELETE FROM tb_files WHERE SUBCATEGORY_ID = p_SUBCATEGORY_ID and CATEGORY_ID = p_CATEGORY_ID;
    ELSE
		SET @ERROR = 1; -- Access Denied!
    END IF;
    -- SELECT @ERROR;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `sp_ADMIN_FILE_SUBCATEGORY_LIST` (IN `p_USERNAME` VARCHAR(20) CHARSET utf8, IN `p_PASSWORD` CHAR(32) CHARSET utf8, IN `p_CATEGORY_ID` INT)  BEGIN
	SET @ERROR = 0;
    SET @ADMIN_ID = (SELECT ID FROM tb_admin WHERE USERNAME = p_USERNAME and PASSWORD = p_PASSWORD);
    
    IF (@ADMIN_ID > 0) THEN
		SELECT * from tb_file_subcategory WHERE CATEGORY_ID = p_CATEGORY_ID;
    ELSE
		SET @ERROR = 1; -- Access Denied!
    END IF;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `sp_ADMIN_FILE_SUBCATEGORY_NAME` (IN `p_SUBCATEGORY_ID` INT UNSIGNED)  BEGIN
	SELECT SUBCATEGORY_NAME FROM tb_file_subcategory WHERE ID = p_SUBCATEGORY_ID;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `sp_ADMIN_FILE_UPLOAD` (IN `p_USERNAME` VARCHAR(20) charset utf8, IN `p_PASSWORD` CHAR(32) charset utf8, IN `p_CATEGORY_ID` INT UNSIGNED, IN `p_SUBCATEGORY_ID` INT UNSIGNED, IN `p_FILE_NAME` VARCHAR(100) charset utf8, IN `p_FILE_SIZE` VARCHAR(10) charset utf8, IN `p_FILE_DESCRIPTION` VARCHAR(500) charset utf8, IN `p_FILE_NO` VARCHAR(10) charset utf8)  BEGIN
	SET @ERROR = 0;
    SET @ADMIN_ID = (SELECT ID FROM tb_admin WHERE USERNAME = p_USERNAME and PASSWORD = p_PASSWORD);
    IF (@ADMIN_ID > 0) THEN
		INSERT INTO tb_files(
			UPLOAD_MANAGER_ID,
            CATEGORY_ID,
            SUBCATEGORY_ID,
            FILE_NAME,
            FILE_SIZE,
            FILE_DESCRIPTION,
            FILE_NO
        ) VALUES (
			@ADMIN_ID,
            p_CATEGORY_ID,
            p_SUBCATEGORY_ID,
            p_FILE_NAME,
            p_FILE_SIZE,
            p_FILE_DESCRIPTION,
            p_FILE_NO
        );
    ELSE
		SET @ERROR = 1; -- Access Denied!
    END IF;
    SELECT @ERROR;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `sp_ADMIN_INACTIVATION_ACCOUNT` (IN `p_USERNAME` VARCHAR(20) charset utf8, IN `p_PASSWORD` CHAR(32) charset utf8, IN `p_ACCOUNT_USERNAME` VARCHAR(20) charset utf8)  BEGIN
	SET @ERROR = 0;
    SET @ADMIN_USERNAME = (SELECT USERNAME FROM tb_admin WHERE USERNAME = p_USERNAME and PASSWORD = p_PASSWORD);
    IF (@ADMIN_USERNAME = 'admin') THEN
		IF NOT (p_ACCOUNT_USERNAME = 'admin') THEN
            SET @ACCOUNT_ID = (SELECT ID FROM tb_admin WHERE USERNAME = p_ACCOUNT_USERNAME);
            IF (@ACCOUNT_ID > 0) THEN
				UPDATE tb_admin SET INACTIVE_ACCOUNT = true WHERE ID = @ACCOUNT_ID;
			ELSE
				SET @ERROR = 3; -- Not Found Username!
            END IF;
        ELSE
			SET @ERROR = 2; -- Invalid Action For Admin!
        END IF;
    ELSE
		SET @ERROR = 1; -- Access Denied (Only Allowed For Admin!)
    END IF;
    SELECT @ERROR;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `sp_ADMIN_INACTIVE_ACCOUNT_LIST` ()  BEGIN
	SELECT ID as ADMIN_ID ,FIRST_NAME,LAST_NAME, USERNAME, EMAIL, PHONE_NUMBER, date_format(REG_DATE, '%d.%m.%Y %H:%i:%s') AS REG_DATE , LAST_LOGIN_IP_ADDRESS FROM tb_admin WHERE INACTIVE_ACCOUNT = 1;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `sp_ADMIN_LOGIN` (IN `p_USERNAME` VARCHAR(20) charset utf8, IN `p_PASSWORD` CHAR(32) charset utf8, IN `p_LAST_LOGIN_IP_ADDRESS` CHAR(15) charset utf8)  BEGIN
	SET @ERROR = 0;
    
    SET @ADMIN_ID = (SELECT ID FROM tb_admin WHERE USERNAME = p_USERNAME and PASSWORD = p_PASSWORD);
    IF (@ADMIN_ID > 0) THEN
		SET @INACTIVE_ACCOUNT = (SELECT INACTIVE_ACCOUNT FROM tb_admin WHERE USERNAME = p_USERNAME and PASSWORD = p_PASSWORD);
        IF (!@INACTIVE_ACCOUNT) THEN
			SET @VERIFY_CODE = (SELECT VERIFY_CODE FROM tb_admin WHERE USERNAME = p_USERNAME and PASSWORD = p_PASSWORD);
            IF (@VERIFY_CODE is null) THEN
				UPDATE tb_admin SET LAST_LOGIN_IP_ADDRESS = p_LAST_LOGIN_IP_ADDRESS WHERE ID = @ADMIN_ID;
                SELECT ID as ADMIN_ID ,FIRST_NAME,LAST_NAME, USERNAME, EMAIL, PHONE_NUMBER, date_format(REG_DATE, '%d.%m.%Y') AS REG_DATE FROM tb_admin WHERE USERNAME = p_USERNAME and PASSWORD = p_PASSWORD;
            ELSE
				SET @ERROR = 3; -- Access Denied (Unverified!)
            END IF;
		ELSE
			SET @ERROR = 2; -- Access Denied (Inactive Account!) 
        END IF;
	ELSE
		SET @ERROR = 1; -- Access Denied (Username or Password Incorrect!)
    END IF;
    
    SELECT @ERROR;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `sp_ADMIN_MEMBER_DELETE` (IN `p_USERNAME` VARCHAR(20) charset utf8, IN `p_PASSWORD` CHAR(32) charset utf8, IN `p_EMAIL` VARCHAR(200) charset utf8)  BEGIN
	SET @ERROR = 0;
    SET @ADMIN_ID = (SELECT ID FROM tb_admin WHERE USERNAME = p_USERNAME and PASSWORD = p_PASSWORD);
    
    IF (@ADMIN_ID > 0) THEN
		SET @MEMBER_ID = (SELECT ID FROM tb_members WHERE EMAIL = p_EMAIL);
		IF (@MEMBER_ID > 0) THEN
			UPDATE tb_members SET DELETED_ACCOUNT = 1 WHERE ID = @MEMBER_ID;
		ELSE
			SET @ERROR = 2; -- Member Not Found!
        END IF;
    ELSE
		SET @ERROR = 1; -- Access Denied!
    END IF;
    SELECT @ERROR;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `sp_ADMIN_RENEW_PASSWORD` (IN `p_USERNAME` VARCHAR(20) charset utf8, IN `p_PASSWORD` CHAR(32) charset utf8, IN `p_NEW_PASSWORD` CHAR(32) charset utf8)  BEGIN
	SET @ERROR = 0;
    SET @ADMIN_ID = (SELECT ID FROM tb_admin WHERE USERNAME = p_USERNAME and PASSWORD = p_PASSWORD);
    IF (@ADMIN_ID > 0) THEN
		UPDATE tb_admin SET PASSWORD = p_NEW_PASSWORD WHERE ID = @ADMIN_ID;
    ELSE
		SET @ERROR = 1; -- Access Denied (Username or Password Incorrect!)
    END IF;
    SELECT @ERROR;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `sp_ADMIN_UPDATE_ACCOUNT` (IN `p_USERNAME` VARCHAR(20) charset utf8, IN `p_PASSWORD` CHAR(32) charset utf8, IN `p_NEW_FIRST_NAME` VARCHAR(100) charset utf8, IN `p_NEW_LAST_NAME` VARCHAR(100) charset utf8, IN `p_NEW_EMAIL` VARCHAR(200) charset utf8, IN `p_NEW_PHONE_NUMBER` CHAR(11) charset utf8)  BEGIN
	SET @ERROR = 0;
    SET @ADMIN_ID = (SELECT ID FROM tb_admin WHERE USERNAME = p_USERNAME and PASSWORD = p_PASSWORD);
    
    IF (@ADMIN_ID > 0) THEN
		IF NOT EXISTS (SELECT ID FROM tb_admin WHERE EMAIL = p_NEW_EMAIL) THEN
			UPDATE tb_admin SET
				FIRST_NAME = p_NEW_FIRST_NAME,
				LAST_NAME = p_NEW_LAST_NAME,
				EMAIL = p_NEW_EMAIL,
				PHONE_NUMBER = p_NEW_PHONE_NUMBER
			WHERE
				ID = @ADMIN_ID;
        ELSE
			SET @ERROR = 2; -- Email already exists!
        END IF;
    ELSE
		SET @ERROR = 1; -- Access Denied (Username or Password Incorrect!)
    END IF;
    SELECT @ERROR;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `sp_ADMIN_WEEKLY_MEMBER_STATISTICS` ()  BEGIN
	SET @NOW =  (SELECT date_format(SYSDATE(), '%Y-%m-%d'));
    SET @DAY = (SELECT date_format(SYSDATE(), '%w')); 
    /*
		DAY 0: SUN, Pazar
        DAY 1: MON, Pazartesi
        DAY 2: TUE, Salı
        DAY 3: WED, Çarşamba
        DAY 4: THU, Perşembe
        DAY 5: FRI, Cuma 
        DAT 6: SAT, Cumartesi
    */
    
    SET @SUN = 0;
    SET @MON = 0;
    SET @TUE = 0;
    SET @WED = 0;
    SET @THU = 0;
    SET @FRI = 0;
    SET @SAT = 0;
    
    IF @DAY = 0 THEN
        SET @MON = 0;
        SET @TUE = 0;
        SET @WED = 0;
        SET @THU = 0;
        SET @FRI = 0;
        SET @SAT = 0;
        SET @SUN = @NOW;
    END IF;
    
    IF @DAY = 1 THEN
        SET @MON = @NOW;
        SET @TUE = 0;
        SET @WED = 0;
        SET @THU = 0;
        SET @FRI = 0;
        SET @SAT = 0;
        SET @SUN = 0;
    END IF;
    
    IF @DAY = 2 THEN
        SET @MON = (SELECT DATE_ADD(@NOW, INTERVAL -1 DAY));
        SET @TUE = @NOW;
        SET @WED = 0;
        SET @THU = 0;
        SET @FRI = 0;
        SET @SAT = 0;
        SET @SUN = 0;
    END IF;
    
    IF @DAY = 3 THEN
        SET @MON = (SELECT DATE_ADD(@NOW, INTERVAL -2 DAY));
        SET @TUE = (SELECT DATE_ADD(@NOW, INTERVAL -1 DAY));
        SET @WED = @NOW;
        SET @THU = 0;
        SET @FRI = 0;
        SET @SAT = 0;
        SET @SUN = 0;
    END IF;
    
    IF @DAY = 4 THEN
		
        SET @MON = (SELECT DATE_ADD(@NOW, INTERVAL -3 DAY));
        SET @TUE = (SELECT DATE_ADD(@NOW, INTERVAL -2 DAY));
        SET @WED = (SELECT DATE_ADD(@NOW, INTERVAL -1 DAY));
        SET @THU = @NOW;
        SET @FRI = 0;
        SET @SAT = 0;
        SET @SUN = 0;
    END IF;
    
    IF @DAY = 5 THEN
		
        SET @MON = (SELECT DATE_ADD(@NOW, INTERVAL -4 DAY));
        SET @TUE = (SELECT DATE_ADD(@NOW, INTERVAL -3 DAY));
        SET @WED = (SELECT DATE_ADD(@NOW, INTERVAL -2 DAY));
        SET @THU = (SELECT DATE_ADD(@NOW, INTERVAL -1 DAY));
        SET @FRI = @NOW;
        SET @SAT = 0;
        SET @SUN = 0;
    END IF;
    
    IF @DAY = 6 THEN
        SET @MON = (SELECT DATE_ADD(@NOW, INTERVAL -5 DAY));
        SET @TUE = (SELECT DATE_ADD(@NOW, INTERVAL -4 DAY));
        SET @WED = (SELECT DATE_ADD(@NOW, INTERVAL -3 DAY));
        SET @THU = (SELECT DATE_ADD(@NOW, INTERVAL -2 DAY));
        SET @FRI = (SELECT DATE_ADD(@NOW, INTERVAL -1 DAY));
        SET @SAT = @NOW;
        SET @SUN = 0;
    END IF;

	SET @SUN_TOTAL = (SELECT COUNT(ID) FROM tb_members WHERE DATE(REG_DATE) = @SUN);
    SET @MON_TOTAL = (SELECT COUNT(ID) FROM tb_members WHERE DATE(REG_DATE) = @MON);
    SET @TUE_TOTAL = (SELECT COUNT(ID) FROM tb_members WHERE DATE(REG_DATE) = @TUE);
    SET @WED_TOTAL = (SELECT COUNT(ID) FROM tb_members WHERE DATE(REG_DATE) = @WED);
    SET @THU_TOTAL = (SELECT COUNT(ID) FROM tb_members WHERE DATE(REG_DATE) = @THU);
    SET @FRI_TOTAL = (SELECT COUNT(ID) FROM tb_members WHERE DATE(REG_DATE) = @FRI);
    SET @SAT_TOTAL = (SELECT COUNT(ID) FROM tb_members WHERE DATE(REG_DATE) = @SAT);
    
    SELECT 
    @MON_TOTAL AS MON, @TUE_TOTAL AS TUE, @WED_TOTAL AS WED, 
    @THU_TOTAL AS THU, @FRI_TOTAL AS FRI, @SAT_TOTAL AS SAT,
    @SUN_TOTAL AS SUN;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `sp_MEMBER_REGISTER` (IN `p_FIRST_NAME` VARCHAR(100) charset utf8, IN `p_LAST_NAME` VARCHAR(100) charset utf8, IN `p_EMAIL` VARCHAR(200) charset utf8, IN `p_ONESIGNAL_PLAYER_ID` CHAR(36) charset utf8, IN `p_IP_ADDRESS` CHAR(15) charset utf8)  BEGIN
	SET @ERROR = 0;
    IF NOT EXISTS (SELECT ID FROM tb_members WHERE EMAIL = p_EMAIL) THEN
		INSERT INTO tb_members (
			FIRST_NAME,
            LAST_NAME,
            EMAIL,
            ONESIGNAL_PLAYER_ID,
            IP_ADDRESS
        ) VALUES (
			p_FIRST_NAME,
            p_LAST_NAME,
            p_EMAIL,
            p_ONESIGNAL_PLAYER_ID,
            p_IP_ADDRESS
        );
    ELSE
		SET @ERROR = 1; -- Email already exists!
    END IF;
    SELECT @ERROR;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `sp_USER_CATEGORY_LIST` ()  BEGIN
	SELECT ID AS CATEGORY_ID, CATEGORY_NAME from tb_file_category;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `sp_USER_FILE_LIST` (IN `p_CATEGORY_ID` INT UNSIGNED, IN `p_SUBCATEGORY_ID` INT UNSIGNED)  BEGIN
	SELECT FILE_NAME AS FILE_TITLE ,FILE_SIZE,FILE_DESCRIPTION, FILE_NO AS FILE_NAME ,date_format(REG_DATE, '%d.%m.%Y') AS FILE_UPLOAD_DATE FROM tb_files WHERE CATEGORY_ID = p_CATEGORY_ID and SUBCATEGORY_ID = p_SUBCATEGORY_ID;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `sp_USER_SUBCATEGORY_LIST` (IN `p_CATEGORY_ID` INT UNSIGNED)  BEGIN
	SELECT ID AS SUBCATEGORY_ID, SUBCATEGORY_NAME from tb_file_subcategory WHERE CATEGORY_ID = p_CATEGORY_ID;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `tb_admin`
--

CREATE TABLE `tb_admin` (
  `ID` int(10) UNSIGNED NOT NULL,
  `AUTH_ADMIN_ID` int(10) UNSIGNED NOT NULL,
  `FIRST_NAME` varchar(100) CHARACTER SET utf8 NOT NULL,
  `LAST_NAME` varchar(100) CHARACTER SET utf8 NOT NULL,
  `USERNAME` varchar(20) CHARACTER SET utf8 NOT NULL,
  `EMAIL` varchar(200) CHARACTER SET utf8 NOT NULL,
  `PHONE_NUMBER` char(11) CHARACTER SET utf8 DEFAULT NULL,
  `VERIFY_CODE` char(6) CHARACTER SET utf8 DEFAULT NULL,
  `PASSWORD` char(32) CHARACTER SET utf8 NOT NULL,
  `LAST_LOGIN_IP_ADDRESS` char(15) CHARACTER SET utf8 NOT NULL,
  `INACTIVE_ACCOUNT` bit(1) NOT NULL DEFAULT b'0',
  `REG_DATE` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Tablo döküm verisi `tb_admin`
--

INSERT INTO `tb_admin` (`ID`, `AUTH_ADMIN_ID`, `FIRST_NAME`, `LAST_NAME`, `USERNAME`, `EMAIL`, `PHONE_NUMBER`, `VERIFY_CODE`, `PASSWORD`, `LAST_LOGIN_IP_ADDRESS`, `INACTIVE_ACCOUNT`, `REG_DATE`) VALUES
(1, 0, 'İlkcan', 'DOĞAN', 'admin', 'ilkcandogan@xx.com', '05xxxxxxxxx', NULL, 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx', '::1', b'0', '2020-09-07 14:52:05');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `tb_files`
--

CREATE TABLE `tb_files` (
  `ID` int(10) UNSIGNED NOT NULL,
  `UPLOAD_MANAGER_ID` int(10) UNSIGNED NOT NULL,
  `CATEGORY_ID` int(10) UNSIGNED NOT NULL,
  `SUBCATEGORY_ID` int(10) UNSIGNED NOT NULL,
  `FILE_NAME` varchar(100) CHARACTER SET utf8 NOT NULL,
  `FILE_SIZE` varchar(10) CHARACTER SET utf8 NOT NULL,
  `FILE_DESCRIPTION` varchar(500) CHARACTER SET utf8 NOT NULL,
  `FILE_NO` varchar(10) CHARACTER SET utf8 NOT NULL,
  `TOTAL_DOWNLOAD` int(10) UNSIGNED DEFAULT 0,
  `REG_DATE` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `tb_file_category`
--

CREATE TABLE `tb_file_category` (
  `ID` int(10) UNSIGNED NOT NULL,
  `CATEGORY_NAME` varchar(100) CHARACTER SET utf8 NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


--
-- Tablo için tablo yapısı `tb_file_subcategory`
--

CREATE TABLE `tb_file_subcategory` (
  `ID` int(10) UNSIGNED NOT NULL,
  `CATEGORY_ID` int(10) UNSIGNED NOT NULL,
  `SUBCATEGORY_NAME` varchar(100) CHARACTER SET utf8 NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Tablo için tablo yapısı `tb_members`
--

CREATE TABLE `tb_members` (
  `ID` int(10) UNSIGNED NOT NULL,
  `FIRST_NAME` varchar(100) CHARACTER SET utf8 NOT NULL,
  `LAST_NAME` varchar(100) CHARACTER SET utf8 NOT NULL,
  `EMAIL` varchar(200) CHARACTER SET utf8 NOT NULL,
  `ONESIGNAL_PLAYER_ID` char(36) CHARACTER SET utf8 NOT NULL,
  `IP_ADDRESS` char(15) CHARACTER SET utf8 NOT NULL,
  `DELETED_ACCOUNT` bit(1) NOT NULL DEFAULT b'0',
  `REG_DATE` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Tablo döküm verisi `tb_members`
--

INSERT INTO `tb_members` (`ID`, `FIRST_NAME`, `LAST_NAME`, `EMAIL`, `ONESIGNAL_PLAYER_ID`, `IP_ADDRESS`, `DELETED_ACCOUNT`, `REG_DATE`) VALUES
(1, 'İlkcan', 'Doğan', 'ilkcandogan@xx.com', 'None', '000.000.000.000', b'0', '2020-10-30 20:48:21');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `tb_member_transactions`
--

CREATE TABLE `tb_member_transactions` (
  `ID` int(10) UNSIGNED NOT NULL,
  `USER_ID` int(10) UNSIGNED NOT NULL,
  `DOWNLOAD_FILE_ID` int(10) UNSIGNED DEFAULT 0,
  `TRANSACTION_DATE` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dökümü yapılmış tablolar için indeksler
--

--
-- Tablo için indeksler `tb_admin`
--
ALTER TABLE `tb_admin`
  ADD PRIMARY KEY (`ID`);

--
-- Tablo için indeksler `tb_files`
--
ALTER TABLE `tb_files`
  ADD PRIMARY KEY (`ID`);

--
-- Tablo için indeksler `tb_file_category`
--
ALTER TABLE `tb_file_category`
  ADD PRIMARY KEY (`ID`);

--
-- Tablo için indeksler `tb_file_subcategory`
--
ALTER TABLE `tb_file_subcategory`
  ADD PRIMARY KEY (`ID`);

--
-- Tablo için indeksler `tb_members`
--
ALTER TABLE `tb_members`
  ADD PRIMARY KEY (`ID`);

--
-- Tablo için indeksler `tb_member_transactions`
--
ALTER TABLE `tb_member_transactions`
  ADD PRIMARY KEY (`ID`);

--
-- Dökümü yapılmış tablolar için AUTO_INCREMENT değeri
--

--
-- Tablo için AUTO_INCREMENT değeri `tb_admin`
--
ALTER TABLE `tb_admin`
  MODIFY `ID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- Tablo için AUTO_INCREMENT değeri `tb_files`
--
ALTER TABLE `tb_files`
  MODIFY `ID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- Tablo için AUTO_INCREMENT değeri `tb_file_category`
--
ALTER TABLE `tb_file_category`
  MODIFY `ID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- Tablo için AUTO_INCREMENT değeri `tb_file_subcategory`
--
ALTER TABLE `tb_file_subcategory`
  MODIFY `ID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- Tablo için AUTO_INCREMENT değeri `tb_members`
--
ALTER TABLE `tb_members`
  MODIFY `ID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Tablo için AUTO_INCREMENT değeri `tb_member_transactions`
--
ALTER TABLE `tb_member_transactions`
  MODIFY `ID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

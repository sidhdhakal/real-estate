-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 28, 2025 at 04:18 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `realestatephp`
--

-- --------------------------------------------------------

--
-- Table structure for table `about`
--

CREATE TABLE `about` (
  `id` int(10) NOT NULL,
  `title` varchar(100) NOT NULL,
  `content` longtext NOT NULL,
  `image` varchar(300) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `about`
--

INSERT INTO `about` (`id`, `title`, `content`, `image`) VALUES
(11, 'HELLO', 'Welcome to Real Estate PHP.\r\nWe are passionate about helping people find the perfect place to call home. Our platform connects property seekers with trusted sellers and agents in a simple, secure, and reliable way.\r\n\r\nWith features like digital signature verification, appointment booking, and feedback encryption, we provide more than just listings — we offer peace of mind.\r\n\r\nWhat We Offer:\r\n\r\nVerified Properties\r\n\r\nDirect Contact with Agents\r\n\r\nAppointment Booking System\r\n\r\nClean and Responsive Design\r\n\r\nSecure Feedback System\r\n\r\nOur Vision:\r\nTo create a trustworthy real estate experience that saves time, builds confidence, and helps people find their ideal property — whether buying, selling, or renting.\r\n\r\nThank you for choosing us as your property partner.', 'ChatGPT Image May 20, 2025, 08_53_42 AM.png');

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `aid` int(10) NOT NULL,
  `auser` varchar(50) NOT NULL,
  `aemail` varchar(50) NOT NULL,
  `apass` varchar(50) NOT NULL,
  `adob` date NOT NULL,
  `aphone` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`aid`, `auser`, `aemail`, `apass`, `adob`, `aphone`) VALUES
(11, 'admin', 'admin@gmail.com', 'admin', '2013-06-04', '9840319999');

-- --------------------------------------------------------

--
-- Table structure for table `appointment`
--

CREATE TABLE `appointment` (
  `appid` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `pid` int(11) NOT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `message` text DEFAULT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'Pending',
  `agent_uid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointment`
--

INSERT INTO `appointment` (`appid`, `uid`, `title`, `pid`, `date`, `time`, `message`, `status`, `agent_uid`) VALUES
(4, 43, 'kthm hiuse', 78, '2025-05-22', '13:59:00', '', 'completed', 45),
(5, 67, 'Tokha New House', 86, '2025-06-22', '09:55:00', '', 'confirmed', 66),
(6, 72, 'kalanki new house', 87, '2025-06-11', '17:43:00', '', 'Pending', 66),
(7, 72, 'kalanki new house', 87, '2025-06-27', '13:43:00', '', 'Pending', 66),
(8, 72, 'kalanki new house', 87, '2025-06-27', '13:45:00', '', 'Pending', 66),
(9, 72, 'kalanki new house', 87, '2025-06-30', '17:47:00', '', 'Pending', 66),
(10, 73, 'New House in Bandipur', 93, '2025-06-11', '08:31:00', '', 'Pending', 72),
(11, 73, 'New House in Bandipur', 93, '2025-06-08', '08:33:00', '', 'Pending', 72),
(12, 73, 'New House in Bandipur', 93, '2025-06-18', '08:37:00', '', 'Pending', 72),
(13, 73, 'kalanki new house', 87, '2025-06-12', '10:38:00', '', 'Pending', 66),
(14, 73, 'kalanki new house', 87, '2025-06-10', '08:43:00', '', 'Pending', 66);

-- --------------------------------------------------------

--
-- Table structure for table `city`
--

CREATE TABLE `city` (
  `cid` int(50) NOT NULL,
  `cname` varchar(100) NOT NULL,
  `sid` int(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `city`
--

INSERT INTO `city` (`cid`, `cname`, `sid`) VALUES
(15, 'Lalitpur', 4),
(16, 'kathmandu', 4),
(17, 'Bhaktapur', 4),
(18, 'Pokhara', 7),
(19, 'Hetuda', 4),
(20, 'Bharatpur', 4),
(21, 'Damauli', 7);

-- --------------------------------------------------------

--
-- Table structure for table `contact`
--

CREATE TABLE `contact` (
  `cid` int(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `subject` varchar(100) NOT NULL,
  `message` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `fid` int(50) NOT NULL,
  `uid` int(50) NOT NULL,
  `fdescription` varchar(300) NOT NULL,
  `status` int(1) NOT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`fid`, `uid`, `fdescription`, `status`, `date`) VALUES
(12, 65, 'YDFXNnTcRUt8jiOAU1NGOf+r5AcWk3rnend6GvqwCk4=', 0, '2025-06-20 11:25:55'),
(14, 65, '6d7oZf3LIwsR2fIPijM2/x6j9iIE1hQbT0IBQsGDCVU=', 0, '2025-06-20 11:29:16'),
(15, 65, '6d7oZf3LIwsR2fIPijM2/x6j9iIE1hQbT0IBQsGDCVU=', 0, '2025-06-20 11:32:07'),
(16, 65, '6d7oZf3LIwsR2fIPijM2/x6j9iIE1hQbT0IBQsGDCVU=', 0, '2025-06-20 11:34:35'),
(17, 65, 'idda0GyYOc7SMr0NVQXGeA==', 0, '2025-06-20 11:34:45'),
(18, 65, 'idda0GyYOc7SMr0NVQXGeA==', 0, '2025-06-20 11:34:56'),
(19, 65, 'idda0GyYOc7SMr0NVQXGeA==', 0, '2025-06-20 11:42:06'),
(20, 65, 'LyYCnHckMUaqLkQCw0Iviw==', 0, '2025-06-20 11:42:16'),
(23, 72, 'eevkHTNXMZAFchLNS2DnNw==', 0, '2025-06-27 15:00:44'),
(25, 74, 'JLlBEt5maOxfK16sr73TQA==', 0, '2025-06-28 08:00:25');

-- --------------------------------------------------------

--
-- Table structure for table `property`
--

CREATE TABLE `property` (
  `pid` int(50) NOT NULL,
  `title` varchar(200) NOT NULL,
  `pcontent` longtext NOT NULL,
  `type` varchar(100) NOT NULL,
  `bhk` varchar(50) NOT NULL,
  `stype` varchar(100) NOT NULL,
  `bedroom` int(50) NOT NULL,
  `bathroom` int(50) NOT NULL,
  `balcony` int(50) NOT NULL,
  `kitchen` int(50) NOT NULL,
  `hall` int(50) NOT NULL,
  `floor` varchar(50) NOT NULL,
  `size` int(50) NOT NULL,
  `price` int(50) NOT NULL,
  `location` varchar(200) NOT NULL,
  `city` varchar(100) NOT NULL,
  `state` varchar(100) NOT NULL,
  `pimage` varchar(300) NOT NULL,
  `pimage1` varchar(300) NOT NULL,
  `pimage2` varchar(300) NOT NULL,
  `pimage3` varchar(300) NOT NULL,
  `pimage4` varchar(300) NOT NULL,
  `uid` int(50) NOT NULL,
  `status` varchar(50) NOT NULL,
  `totalfloor` varchar(50) NOT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp(),
  `isFeatured` int(11) DEFAULT NULL,
  `digital_signature` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `property`
--

INSERT INTO `property` (`pid`, `title`, `pcontent`, `type`, `bhk`, `stype`, `bedroom`, `bathroom`, `balcony`, `kitchen`, `hall`, `floor`, `size`, `price`, `location`, `city`, `state`, `pimage`, `pimage1`, `pimage2`, `pimage3`, `pimage4`, `uid`, `status`, `totalfloor`, `date`, `isFeatured`, `digital_signature`) VALUES
(76, 'sandesh 1', 'thie is prop 1', 'building', '2 BHK', 'rent', 3, 2, 4, 3, 3, '3rd Floor', 342, 431, 'kathmandu', 'kathmandu', 'bagmati', 'cartoon.jpg', 'cartoon.jpg', 'cartoon.jpg', 'cartoon.jpg', 'cartoon.jpg', 43, 'available', '8 Floor', '2025-05-26 13:18:33', 1, 'o3BXz4YE/eaFtvosc6iiQHpuOClsnrN0bncuYqW1LMVCVjm/lPXQLtKMLvLiypxuC1Am0sfvFHZC4Et9LTSFJpWSqM7UZ5Olug/o+QIrAjVfvW56dbBJNxeRG9cpqEQbgFmVVwMNxAY0XHEipmAxz8PEfq3PIPJvCAVJu7OFINtLXrpCFaS3to7WokRHMTlYSfd7uZD+Y2WMlYv8tM/owGTajUjFPQn1PydHmuyCh6ixi6keU9SDAAG2MGe2ie88ED0N5jSY64uBKWOSVuvHXHtamp6W+wwCmNKt80rgz4JrGj11kzDKKkmUEzD0mCxTm64gWGXdzFVULp52D3scqw=='),
(78, 'kthm hiuse', 'This is the ramu housr', 'building', '3', 'sale', 3, 2, 4, 3, 3, '3rd Floor', 342, 431, 'kathmandu', 'kathmandu', 'bagmati', '78_1748575509_download__2_.jpg', '78_1748575509_download__3_.jpg', '78_1748575509_download__3_.jpg', '78_1748575509_download__3_.jpg', '78_1748575509_download__2_.jpg', 45, 'inactive', '3', '2025-05-30 09:00:02', 1, 'nj4TJjFHF0dUaflH3wM7y4dmvdwVRdA8ausQ76bbPuOYG1ZZ8MDdOFGn8CWbEsU14jqb87eFx9Je2lNVPJhV9zuOmTNG2sYaC+cPwky+UvwN+vY18yj8wzS+8g20tKCbg2mpfyFnane9S1rkw3nrjgkHZE/y39rWbQbSDRt9Hb8mKWPWbLvmurlzDYIheYkOiWBmDOBIHMV6FmXgz1I1cZgakOuYsMNK6SOe9n9yK6lW0BzJb/jnlD84yfgS9JElcTXz20JNJT1unFo1qTtk3qOsnkAUWcjD0DGW1Zw82pgiXwfl6H6FRQKQXKQ06f5wfT6untDKeJ6+Q/UFwKYZhQ=='),
(101, 'Gongabu House ', 'New House in Gongabu Kathmandu.', 'building', '3 BHK', 'sale', 8, 3, 4, 2, 4, '3rd Floor', 8765, 8000000, 'Kathmandu,Nepal', 'Kathmandu', 'Bagmati', '7.1.jpg', '7.2.jpg', '7.3.jpg', '7,4.jpg', '7.5.jpg', 72, 'available', '3 Floor', '2025-06-28 07:52:43', 1, 'vh5gSHL/lWfNbnc/8GzBZm2h5dhzlEdiOCwXwI4xdjrnVycvqPZHG5nabKV0EGBm9Sw78Rz6UEd+bfdfk03ambolCh/a3CHZHHQFPM8AguOOEs/XG5MttWK+u7xufk44w1+0y8ixQwQsN646lWIUlcrCGFDgUqI9EWUasoSooX46FbfhFoANqpSWm4GDwBgPqLIJcVx6tLbafbbA9xCcLjC7ki6seKo9RLSjheXP61c6Fc+/zRCaLdeQRg3r7A6phzLds3lBoG4nmuaI34hbS8JsSfqKPiILiNc3KEuFVuxNBO8sg60uc922ikAnSx/ci0f6yFwOi8pozIZAm/o2ww=='),
(102, 'Chakrapath New  House Sale ', 'New House in Chakrapath', 'building', '3 BHK', 'sale', 8, 3, 4, 2, 4, '2nd Floor', 8907, 7000000, 'Kathmandu,Nepal', 'Kathmandu', 'Bagmati', '8.1.jpg', '8.2.webp', '8.3.jpg', '8.4.jpg', '8.5.jpg', 72, 'available', '3 Floor', '2025-06-28 07:55:00', 1, 'kHai4NYDRr1Ehf2w6gVjpaUlKL4M9iLHWt1U1XDv6XcRdasFdaYCLX+SPy57W2CA6VT8MSxdsFMeAyBD2jmFbViRRPtkzc+ewIYnJEyrDE3a+oAgZfeN7RZ9narBZMWhiUou5shW2vpdDPYEnIQSuU+V2FMKvz/BgnMwFBmjiwkf24t91KH22/h/siipBhnLTKZ4oKEBZyX1PSvwZD28rxTrYz5lbp0EG/UFDKp2gYzmAjLXPmeY/hF3E8o/bT4wZt9U5UYVkp3sjTCgKsZlXjeyz4b9lBS8JA0jqC9XnP2p/DLX5MnkXRetFlOaWx2IpM9fAfc/+Cg28Kg5kwZ3Dg=='),
(103, 'Lagankhel House ', 'New House in Lagankhel', 'building', '5 BHK', 'sale', 8, 3, 3, 2, 4, '1st Floor', 3903, 6000000, 'Lalitpur,Nepal', 'Lalitpur', 'Bagmati', '9.1.jpg', '9.2.jpg', '9.3.jpg', '9.4.jpg', '9.5.jpg', 72, 'available', '5 Floor', '2025-06-28 07:57:39', 1, 'cwq+fojNlOpKSaNJA8MwJfxIkZad2pvM6XEA6xs6MFG99LWKfwpLNobBfFh/cIfF8lt2fuW2hmIbRa+Z5z2xXdNBCtlU337TqMrXu/mHx7ECtz9LtBmidlgPg4vdLHfukqZMR/K++vUpOvD1gSMLRsjAU+bRvtvrO9774mYv0tbMAAYGTvy8C/2cfue1agg9+TRoNTWbu12Gb+bwUilcSeyrIoeskAuZIdkmy5NHUQp6wj9uClcPf4hZej1GXLRIfoh38WtzYeZYgwZWPqlTGv1DfEBsjfDEJi9P/bvs9bHBE+RDuEpYYksF8Mjngz0g/HIxMjnvmpA75D4k5wUFUQ==');

-- --------------------------------------------------------

--
-- Table structure for table `state`
--

CREATE TABLE `state` (
  `sid` int(50) NOT NULL,
  `sname` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `state`
--

INSERT INTO `state` (`sid`, `sname`) VALUES
(2, 'Koshi'),
(3, 'Madesh'),
(4, 'Bagmati'),
(7, 'Gandaki'),
(9, 'Lumbini'),
(10, 'Karnali'),
(15, 'Sudhur Pashim');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `uid` int(50) NOT NULL,
  `uname` varchar(100) NOT NULL,
  `uemail` varchar(100) NOT NULL,
  `uphone` varchar(20) NOT NULL,
  `upass` varchar(50) NOT NULL,
  `utype` varchar(50) NOT NULL,
  `uimage` varchar(300) NOT NULL,
  `public_key` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`uid`, `uname`, `uemail`, `uphone`, `upass`, `utype`, `uimage`, `public_key`) VALUES
(66, 'vinish', 'vinish@gmail.com', '9846345345', 'de8e0ab555baf827e4104af6f36b821be8c0579d', 'agent', 'vinish.jpeg', '-----BEGIN PUBLIC KEY-----\nMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA1lGOcvRM4lpoG5lZb0Fo\nnQ+/jQ3jCv2KjxYlYQBp4607kxkuqVrQj21afOaNBNe0xi3Zjx4h84kRl9dru48P\nD1137656FNzYQcQNBHVQ+D62SBEY5ucb5z3a1dpk/gNvb0cqWi0qKEdrmJhUb1jc\nyJqSqEjRijiusYuafNSITR1zdxLhEh1SjN8M7cG85Dn09HNjUu8Y/j+ZcwFYVX/n\nKQ1o1B82eR5Z08u1J/xW7SSuW6Hdo0T9fVYi+j4S03sUHb9JRSAMYvCRat9p7GRD\nptaloe8kBFuqwylwjtlDXq2FV17l/1sN0i85XhGxfzJ1EUQyQCNVE8nuF2FBvygB\nYwIDAQAB\n-----END PUBLIC KEY-----\n'),
(67, 'sanjay', 'sanjay@gmail.com', '9834324233', '25ecbcb559d14a98e4665d6830ac5c99991d7c25', 'user', 'sanjay.jpeg', '-----BEGIN PUBLIC KEY-----\nMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA0QGvf8RbmbxQnCwvtLqX\nSSQhMcUu6poscB8d0rGSpRWxlp5IBTXLpfrJnp3w36tAHIRzQ5t2ukHTLjPcgsHg\nZ8mahyolpRDzcXgb2A0nARQhGlavK5EPAneZBb17eVmDt4ccu1n7Nk3gQwLsyseq\nCj6/xhP8BfC9qV/CHp502Yu1Afib1zQNByjzlrdS/kE8ExuaEYoVyKQXNzcoFSem\nXXGk9GcXkJPDRJBCPF6ELepyBsVIczOdProzKMmJID5RYl7EAfbbM1p+PKXFXtMT\nl/ZfW1X7AFIMmTJ1Slo6UJ+lkatN5b8k3TWylIdaICFYPSSPL2vnZ8+NlGzXLzmO\n9QIDAQAB\n-----END PUBLIC KEY-----\n'),
(70, 'user2', 'user2@gmail.com', '9846345345', '12dea96fec20593566ab75692c9949596833adc9', 'user', 'umesh.jpeg', '-----BEGIN PUBLIC KEY-----\nMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA2KiLrBeDSzZNVPjrRBcv\nmwNr0zpZTtwOk0FXVhEEU90abD8vfXgL6V1bOkDf8cJ/+uQsmDbZf29M4mDFs8V9\nj6TRIxqgD3/s1+z4Y9pcqiGONF7CcrZwF2lzmZQIKpYy9WSSdOKMmCKxgzkC5sG+\nRpqJJCSKMKSKz5JwhNoJ0qjKz13uqtGxL3vUa5+/ytgdFKXbhNlE0alBrORr/1aU\nDlGJRrzp1t2B53Exk6XYd36Lb4IJT6K4IPrrX3fi9B77ZfczKMT2/dzXC1RMG2JY\nwrcyBhGS7XKjSNwxvHrlUWXiNJmqJbjCce7qgIr4lfuuB1JlF4is8VprY6TSec+v\nPwIDAQAB\n-----END PUBLIC KEY-----\n'),
(72, 'sandesh', 'sandesh@gmail.com', '9834324233', '6fbf88f8a5fd0acb95444b8c378076f5477cc9d8', 'agent', 'sandesh.jpeg', '-----BEGIN PUBLIC KEY-----\nMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAyIDQHKNWiJr1BjmcQd+7\ncAn8Rd0oNOW3BcrRrMFmZenuzK5EVd5aiIRRTa4dFvgQ9sCnbvQ35Z2+n+TSvGy2\nwk138WrL1jFWZqS3wiM208jIyhkckjkbnkvWAlo8qsSARyYGMfjoEuqYbyIDzLhN\nTDqEVVP5SPEsz+ztl8mws1sLTVR4oOeuWZ2HroPC6Y4PYIVUhOITf2ZPMQgQnYOl\n8Sy4WXCylDCnwjUyCfXLVguEEQemDMMrwS/LbyhJtIWCSK1F6vu63R5z2V7oNNjR\n9k+S+rDImNvwBiOe1DVy1rC0f6XoDX4/PFTFh43rU7/SHoKMKTxKd5zQ2Iatz7eE\nNQIDAQAB\n-----END PUBLIC KEY-----\n'),
(73, 'sujan pandey', 'sujan@gmail.com', '9834324234', 'c7df0cddfdd89c96014e28615137205bd3511631', 'agent', 'sujan.jpeg', '-----BEGIN PUBLIC KEY-----\nMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA5FYfW+LzhJSczqTarvAU\nNGpuShmzfUQ1Umvd5l4TSwbrIrItG2RrvYB+BNvEIABvfvL+ry/IWt6Ks9zuQdOM\n6UYSy85LyTBBzSlmLkWJYBwAULg49er0GDfY/fzy0qrPfI1MbWNYwzoVvcjLiJxV\nX8xlhj4GqQUbGr1QoYeDIJijFuHHrd7kasjyQE5J7i+ICJfDtHgrWHUVxN6hk0/i\nxZ8JNTv2qksPFCCuoaYnieitk9ESF6bs9g+ewdp2omvqtemqpYYqff5/vFdAhdLb\nFoaWVT8WRNpTHX8m8VuPSSPaGzE+r2At/iKV/9OAyz8Xfo3wojQ996WRSTdYOtEM\naQIDAQAB\n-----END PUBLIC KEY-----\n'),
(74, 'user', 'user@gmail.com', '98463453453', '12dea96fec20593566ab75692c9949596833adc9', 'agent', '10.5.jpg', '-----BEGIN PUBLIC KEY-----\nMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAzolnhLLqCkEJH1f5fu41\njCtViHlzyZWZi7opNj8wg53vtIbeLhOi5oElqYwhiPIGS5UgxCqdNhMBQ5SMSUvZ\nzV9xO+utK9FfX0K6uXv6JNxErsgWIJEn1DTwRt6NKaj96cCtpM1b58F/ZkT46obG\n2IncQSd8tNBiMCsLrQ/JDBZYNZCDXrNR6jSik08ilr2ye3Y32Bg2genyU4OaEVvb\nLH6iEW9e6qzgQvkahCb7LgNQqU7Yi77NXE4KUOfL5tPJpeOfmJtE87KBFpPV5lgv\njLNlpMCCnvRqhJeU4hlJxQHDlEDJoTyv1S5DO4yGGHJybI3E/CPnH8pZjbhZcILR\nPwIDAQAB\n-----END PUBLIC KEY-----\n');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `about`
--
ALTER TABLE `about`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`aid`);

--
-- Indexes for table `appointment`
--
ALTER TABLE `appointment`
  ADD PRIMARY KEY (`appid`),
  ADD KEY `uid` (`uid`),
  ADD KEY `pid` (`pid`),
  ADD KEY `agent_uid` (`agent_uid`);

--
-- Indexes for table `city`
--
ALTER TABLE `city`
  ADD PRIMARY KEY (`cid`);

--
-- Indexes for table `contact`
--
ALTER TABLE `contact`
  ADD PRIMARY KEY (`cid`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`fid`);

--
-- Indexes for table `property`
--
ALTER TABLE `property`
  ADD PRIMARY KEY (`pid`);

--
-- Indexes for table `state`
--
ALTER TABLE `state`
  ADD PRIMARY KEY (`sid`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`uid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `about`
--
ALTER TABLE `about`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `aid` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `appointment`
--
ALTER TABLE `appointment`
  MODIFY `appid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `city`
--
ALTER TABLE `city`
  MODIFY `cid` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `contact`
--
ALTER TABLE `contact`
  MODIFY `cid` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `fid` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `property`
--
ALTER TABLE `property`
  MODIFY `pid` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=104;

--
-- AUTO_INCREMENT for table `state`
--
ALTER TABLE `state`
  MODIFY `sid` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `uid` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=75;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

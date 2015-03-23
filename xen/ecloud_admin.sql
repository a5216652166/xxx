-- phpMyAdmin SQL Dump
-- version 4.0.10.7
-- http://www.phpmyadmin.net
--
-- 主机: localhost
-- 生成日期: 2015-03-23 14:26:53
-- 服务器版本: 5.1.73-log
-- PHP 版本: 5.3.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- 数据库: `ecloud_admin`
--

-- --------------------------------------------------------

--
-- 表的结构 `Console`
--

CREATE TABLE IF NOT EXISTS `Console` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `CodeType` char(50) NOT NULL COMMENT '标记是主机还是虚拟机',
  `Code` char(100) NOT NULL COMMENT '标记号',
  `VNCPort` smallint(6) unsigned NOT NULL COMMENT 'VNC端口',
  `TTY` char(50) NOT NULL COMMENT 'TTY编号',
  `TimeStamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '记录最后更新时间',
  PRIMARY KEY (`ID`),
  UNIQUE KEY `unique_code` (`Code`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=90 ;

--
-- 转存表中的数据 `Console`
--

INSERT INTO `Console` (`ID`, `CodeType`, `Code`, `VNCPort`, `TTY`, `TimeStamp`) VALUES
(71, 'VM', 'A-08-509-26-20150121-001', 5905, '/dev/pts/1', '2015-01-21 07:52:39'),
(70, 'VM', 'A-08-509-26-20150115-005', 5905, '/dev/pts/5', '2015-03-06 02:00:04'),
(69, 'VM', 'A-08-509-26-20150115-004', 5901, '/dev/pts/1', '2015-03-04 08:16:08'),
(68, 'VM', 'Transfer VM for VDI 6428c2fb-da4d-41af-8ece-46f9c8bf1865', 5906, '/dev/pts/8', '2015-02-28 03:32:12'),
(89, 'VM', 'xe6x9dxa5xe8x87xaaxe5xbfxabxe7x85xa7xe2x80x9cBasexe2x80x9dxe7x9ax84xe6xa8xa1xe6x9dxbf (1)', 5902, '/dev/pts/2', '2015-03-23 01:50:02'),
(88, 'VM', 'Test_10.11.0.2', 5901, '/dev/pts/1', '2015-03-23 06:26:51'),
(87, 'VM', '121.201.55.42', 5903, '/dev/pts/2', '2015-03-10 06:42:03'),
(86, 'VM', 'Test_120.201.55.40', 5901, '/dev/pts/1', '2015-03-23 06:26:02'),
(85, 'VM', 'A-08-509-26-20150130-001', 5907, '/dev/pts/3', '2015-01-30 04:57:39'),
(84, 'VM', 'A-08-509-26-20150129-007', 5907, '/dev/pts/6', '2015-01-29 02:18:40'),
(83, 'VM', 'A-08-509-26-20150129-006', 5906, '/dev/pts/6', '2015-01-29 02:16:07'),
(82, 'VM', 'A-08-509-26-20150129-005', 5906, '/dev/pts/7', '2015-01-29 02:11:29'),
(81, 'VM', 'A-08-509-26-20150129-003', 5905, '/dev/pts/1', '2015-01-29 02:00:29'),
(67, 'VM', 'Transfer VM for VDI 50e4e3ed-117f-4a92-a78d-de21ecc22ffd', 5905, '/dev/pts/7', '2015-02-28 03:32:12'),
(80, 'VM', 'A-08-509-26-20150129-002', 5907, '/dev/pts/3', '2015-01-29 01:47:40'),
(79, 'VM', 'A-08-509-26-20150129-001', 5906, '/dev/pts/6', '2015-01-29 01:32:06'),
(78, 'VM', 'A-08-509-26-20150127-005', 5905, '/dev/pts/1', '2015-01-27 09:36:31'),
(77, 'VM', 'A-08-509-26-20150127-004', 5905, '/dev/pts/1', '2015-01-27 09:28:31'),
(76, 'VM', 'A-08-509-26-20150127-003', 5907, '/dev/pts/3', '2015-01-27 09:24:41'),
(75, 'VM', 'A-08-509-26-20150127-002', 5905, '/dev/pts/1', '2015-01-27 09:21:31'),
(74, 'VM', 'A-08-509-26-20150127-001', 5907, '/dev/pts/3', '2015-01-27 09:17:41'),
(73, 'VM', 'squid', 5906, '/dev/pts/6', '2015-01-26 03:41:07'),
(72, 'VM', 'A-08-509-26-20150121-002', 5904, '/dev/pts/5', '2015-02-26 02:56:05'),
(66, 'VM', 'A-08-509-26-20150115-003', 5904, '/dev/pts/6', '2015-01-15 03:03:52'),
(65, 'VM', 'A-08-509-26-20150115-002', 5903, '/dev/pts/4', '2015-01-15 03:04:17'),
(64, 'VM', 'A-08-509-26-20150115-001', 5902, '/dev/pts/4', '2015-01-15 03:03:51'),
(63, 'VM', 'Template_CentOS_6.5_x86_64 (1)', 5904, '/dev/pts/4', '2015-03-08 02:57:05'),
(62, 'VM', 'Test_121.201.55.36', 5903, '/dev/pts/3', '2015-03-23 06:26:51'),
(61, 'VM', 'test_121.201.55.59', 5906, '/dev/pts/6', '2015-03-23 06:26:02'),
(60, 'VM', 'VM_Xen_Dev_Test', 5907, '/dev/pts/7', '2015-03-23 06:26:02'),
(59, 'VM', 'test_121.201.55.41', 5902, '/dev/pts/2', '2015-03-23 06:26:03'),
(58, 'VM', 'Test_121.201.55.37', 5904, '/dev/pts/4', '2015-02-26 06:46:04'),
(57, 'VM', 'Template_CentOS_6.5_x86_64 (2)', 5902, '/dev/pts/2', '2015-03-23 06:26:51'),
(56, 'VM', 'CentOS 6 (64-bit) (1)', 5902, '/dev/pts/3', '2015-03-10 08:43:04');

-- --------------------------------------------------------

--
-- 表的结构 `Group`
--

CREATE TABLE IF NOT EXISTS `Group` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(255) NOT NULL,
  `StockHouseName` varchar(255) NOT NULL,
  `ControlVlanBegin` varchar(255) NOT NULL,
  `ControlVlanEnd` varchar(255) NOT NULL,
  `ControlIPBegin` varchar(255) NOT NULL,
  `ControlIPEnd` varchar(255) NOT NULL,
  `ControlMask` int(2) NOT NULL,
  `ControlGateway` varchar(255) NOT NULL,
  `StorageVlanBegin` varchar(255) NOT NULL,
  `StorageVlanEnd` varchar(255) NOT NULL,
  `StorageIPBegin` varchar(255) NOT NULL,
  `StorageIPEnd` varchar(255) NOT NULL,
  `StorageMask` int(2) NOT NULL,
  `StorageGateway` varchar(255) NOT NULL,
  `State` char(50) NOT NULL DEFAULT 'enabled' COMMENT '标记是否可用',
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- 转存表中的数据 `Group`
--

INSERT INTO `Group` (`ID`, `Name`, `StockHouseName`, `ControlVlanBegin`, `ControlVlanEnd`, `ControlIPBegin`, `ControlIPEnd`, `ControlMask`, `ControlGateway`, `StorageVlanBegin`, `StorageVlanEnd`, `StorageIPBegin`, `StorageIPEnd`, `StorageMask`, `StorageGateway`, `State`) VALUES
(5, 'HJ-G001', '中山火炬', '3000', '3100', '10.11.14.2', '10.11.14.254', 24, '10.11.14.1', '3200', '3300', '10.11.15.2', '10.11.15.254', 24, '10.11.15.1', 'enabled');

-- --------------------------------------------------------

--
-- 表的结构 `GroupIP`
--

CREATE TABLE IF NOT EXISTS `GroupIP` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `GroupID` int(11) NOT NULL,
  `PublicIPType` varchar(50) NOT NULL COMMENT 'VM_BGPIP，VM_IPLCIP,SLB_BGPIP,SLB_IPLCIP,OSS_BGPIP,OSS_IPLCIP',
  `PublicIPBegin` varchar(255) NOT NULL,
  `PublicIPEnd` varchar(255) NOT NULL,
  `PublicMask` int(2) NOT NULL,
  `PublicGateway` varchar(255) NOT NULL,
  `State` char(50) NOT NULL DEFAULT 'enabled' COMMENT '标记是否可用',
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=31 ;

--
-- 转存表中的数据 `GroupIP`
--

INSERT INTO `GroupIP` (`ID`, `GroupID`, `PublicIPType`, `PublicIPBegin`, `PublicIPEnd`, `PublicMask`, `PublicGateway`, `State`) VALUES
(27, 5, 'VM_BGPIP', '121.201.55.32', '121.201.55.63', 27, '121.201.55.33', 'enabled');

-- --------------------------------------------------------

--
-- 表的结构 `Host`
--

CREATE TABLE IF NOT EXISTS `Host` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `PoolCode` varchar(255) NOT NULL,
  `PropertyCode` varchar(255) NOT NULL,
  `Type` char(50) NOT NULL COMMENT '主机所属类型VMHost,SLBHost,OSSHost,StorageHost',
  `State` varchar(50) NOT NULL DEFAULT 'enabled' COMMENT 'enabled -- 可用,  disabled -- 不可用',
  `ControlIP` varchar(255) NOT NULL,
  `ControlGateway` varchar(255) NOT NULL,
  `ControlVlan` varchar(255) NOT NULL,
  `StorageIP` varchar(255) NOT NULL,
  `StorageGateway` varchar(255) NOT NULL,
  `StorageVlan` varchar(255) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=25 ;

--
-- 转存表中的数据 `Host`
--

INSERT INTO `Host` (`ID`, `PoolCode`, `PropertyCode`, `Type`, `State`, `ControlIP`, `ControlGateway`, `ControlVlan`, `StorageIP`, `StorageGateway`, `StorageVlan`) VALUES
(20, 'HJ-G001-P001', 'C-02-505-07-20110523-011', 'VMHost', 'enabled', '10.11.253.43', '10.11.253.1', '3000', '10.11.15.2', '10.11.15.1', '3200'),
(21, 'HJ-G001-P001', 'A-02-505-04-20120429-021', 'VMHost', 'enabled', '10.11.253.44', '10.11.253.1', '3000', '10.11.15.3', '10.11.15.1', '3200'),
(22, 'HJ-G001-P001', 'A-02-505-04-20121126-005', 'VMHost', 'enabled', '10.11.253.45', '10.11.253.1', '3000', '10.11.15.4', '10.11.15.1', '3200'),
(23, 'HJ-G001-P001', '10.11.253.42_ISCSI_Sdc1', 'StorageHost', 'enabled', '10.11.253.42', '', '', '', '', '');

-- --------------------------------------------------------

--
-- 表的结构 `Pool`
--

CREATE TABLE IF NOT EXISTS `Pool` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `GroupID` int(11) NOT NULL,
  `PoolCode` char(200) NOT NULL,
  `Data` text NOT NULL,
  `State` varchar(50) NOT NULL DEFAULT 'enabled' COMMENT 'enabled -- 可用,  disabled -- 不可用',
  `NetVlanBegin` varchar(255) NOT NULL,
  `NetVlanEnd` varchar(255) NOT NULL,
  `PrivateIPBegin` varchar(255) NOT NULL,
  `PrivateIPEnd` varchar(255) NOT NULL,
  `PrivateMask` int(2) NOT NULL,
  `PrivateGateway` varchar(255) NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `unique_pool` (`PoolCode`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

--
-- 转存表中的数据 `Pool`
--

INSERT INTO `Pool` (`ID`, `GroupID`, `PoolCode`, `Data`, `State`, `NetVlanBegin`, `NetVlanEnd`, `PrivateIPBegin`, `PrivateIPEnd`, `PrivateMask`, `PrivateGateway`) VALUES
(7, 5, 'HJ-G001-P001', '{"master":"10.11.253.45","user":"root","pass":"Rjkj@efly#123","vnc_proxy_host":"121.201.55.35","vnc_proxy_port":"9000","uuid":"605c0b0b-d8d4-44a5-bba4-f82b4194186b"}', 'enabled', '2', '201', '10.11.0.2', '10.11.0.254', 24, '10.11.0.1'),
(9, 5, 'test1`22332', '111sssss', 'enabled', '99', '88', '77', '66', 55, '4444');

-- --------------------------------------------------------

--
-- 表的结构 `PoolIP`
--

CREATE TABLE IF NOT EXISTS `PoolIP` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `PoolID` int(11) NOT NULL,
  `PublicIPType` varchar(50) NOT NULL COMMENT 'VM_BGPIP，VM_IPLCIP,SLB_BGPIP,SLB_IPLCIP,OSS_BGPIP,OSS_IPLCIP',
  `PublicIPBegin` varchar(255) NOT NULL,
  `PublicIPEnd` varchar(255) NOT NULL,
  `PublicMask` int(2) NOT NULL,
  `PublicGateway` varchar(255) NOT NULL,
  `State` char(50) NOT NULL DEFAULT 'enabled' COMMENT '标记是否可用',
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=31 ;

--
-- 转存表中的数据 `PoolIP`
--

INSERT INTO `PoolIP` (`ID`, `PoolID`, `PublicIPType`, `PublicIPBegin`, `PublicIPEnd`, `PublicMask`, `PublicGateway`, `State`) VALUES
(27, 7, 'VM_BGPIP', '121.201.55.32', '121.201.55.63', 27, '121.201.55.33', 'enabled'),
(30, 9, 'VM_IPLCIP', '10.0.0.0', '10.255.255.255', 336676, '44666666', 'enabled');

-- --------------------------------------------------------

--
-- 表的结构 `PoolTask`
--

CREATE TABLE IF NOT EXISTS `PoolTask` (
  `ID` int(11) NOT NULL AUTO_INCREMENT COMMENT '任务ID',
  `PoolCode` char(100) NOT NULL,
  `Type` char(50) NOT NULL COMMENT '任务类型',
  `SubType` char(50) NOT NULL COMMENT '任务子类型',
  `SubmitTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '任务提交时间',
  `Data` text NOT NULL COMMENT '任务描述json格式',
  `State` char(50) NOT NULL COMMENT '任务当前状态',
  `Try` int(11) NOT NULL,
  `Tried` int(11) NOT NULL,
  `StartTime` timestamp NULL DEFAULT '0000-00-00 00:00:00' COMMENT '任务开始时间',
  `FinishTime` timestamp NULL DEFAULT '0000-00-00 00:00:00' COMMENT '任务结束时间',
  `Ret` int(11) NOT NULL COMMENT '任务执行返回值',
  `Result` text NOT NULL COMMENT '任务执行返回结果json格式',
  `Error` text NOT NULL COMMENT '任务执行错误信息json格式',
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=16 ;

--
-- 转存表中的数据 `PoolTask`
--

INSERT INTO `PoolTask` (`ID`, `PoolCode`, `Type`, `SubType`, `SubmitTime`, `Data`, `State`, `Try`, `Tried`, `StartTime`, `FinishTime`, `Ret`, `Result`, `Error`) VALUES
(1, 'HJ-G001-P001', 'VMTemplateCreate', '', '2015-01-29 01:58:01', '{"template_code":"Template_CentOS_6.5_x86_64","cpu":"4","ram":"2048","vm_code":"A-08-509-26-20150129-005"}', 'finish', 2, 1, '2015-01-29 01:58:52', '2015-01-29 01:59:17', 0, '', ''),
(2, 'HJ-G001-P001', 'VMNetworkConfig', '', '2015-01-29 01:58:01', '{"vm_code":"A-08-509-26-20150129-005","public_ip":"121.201.55.53","public_vlan":23,"public_mask":"27","public_gateway":"121.201.55.33","private_ip":"10.11.0.23","private_vlan":23,"private_mask":"24","private_gateway":"10.11.0.1"}', 'finish', 2, 0, '2015-01-29 02:00:28', '2015-01-29 02:00:33', 0, '', ''),
(3, 'HJ-G001-P001', 'AddVMDisk', '', '2015-01-29 01:58:01', '{"vm_code":"A-08-509-26-20150129-005","disk_type":"iSCSI_SATA","disk_size":"10","vmdisk_id":1,"storage_code":"10.11.253.42_ISCSI_Sdc1"}', 'finish', 2, 1, '2015-01-29 02:09:58', '2015-01-29 02:10:05', 0, '', ''),
(4, 'HJ-G001-P001', 'VMTemplateCreate', '', '2015-01-29 02:13:33', '{"template_code":"Template_CentOS_6.5_x86_64","cpu":"4","ram":"2048","vm_code":"A-08-509-26-20150129-006"}', 'finish', 2, 1, '2015-01-29 02:13:47', '2015-01-29 02:14:10', 0, '', ''),
(5, 'HJ-G001-P001', 'VMNetworkConfig', '', '2015-01-29 02:13:33', '{"vm_code":"A-08-509-26-20150129-006","public_ip":"121.201.55.54","public_vlan":24,"public_mask":"27","public_gateway":"121.201.55.33","private_ip":"10.11.0.24","private_vlan":24,"private_mask":"24","private_gateway":"10.11.0.1"}', 'finish', 2, 0, '2015-01-29 02:15:07', '2015-01-29 02:15:11', 0, '', ''),
(6, 'HJ-G001-P001', 'AddVMDisk', '', '2015-01-29 02:13:33', '{"vm_code":"A-08-509-26-20150129-006","disk_type":"iSCSI_SATA","disk_size":"10","vmdisk_id":2,"storage_code":"10.11.253.42_ISCSI_Sdc1"}', 'finish', 2, 1, '2015-01-29 02:14:52', '2015-01-29 02:14:58', 0, '', ''),
(7, 'HJ-G001-P001', 'VMTemplateCreate', '', '2015-01-29 02:16:15', '{"template_code":"Template_CentOS_6.5_x86_64","cpu":"4","ram":"2048","vm_code":"A-08-509-26-20150129-007"}', 'finish', 2, 1, '2015-01-29 02:16:47', '2015-01-29 02:17:00', 0, '', ''),
(8, 'HJ-G001-P001', 'VMNetworkConfig', '', '2015-01-29 02:16:15', '{"vm_code":"A-08-509-26-20150129-007","public_ip":"121.201.55.55","public_vlan":25,"public_mask":"27","public_gateway":"121.201.55.33","private_ip":"10.11.0.25","private_vlan":25,"private_mask":"24","private_gateway":"10.11.0.1"}', 'finish', 2, 0, '2015-01-29 02:18:39', '2015-01-29 02:18:42', 0, '', ''),
(9, 'HJ-G001-P001', 'VMTemplateCreate', '', '2015-01-30 03:55:22', '{"template_code":"Template_CentOS_6.5_x86_64","cpu":"1","ram":"512","vm_code":"A-08-509-26-20150130-001"}', 'doing', 2, 1, '2015-01-30 03:55:49', '0000-00-00 00:00:00', 0, '', ''),
(10, 'HJ-G001-P001', 'VMNetworkConfig', '', '2015-01-30 03:55:22', '{"vm_code":"A-08-509-26-20150130-001","public_ip":"121.201.55.56","public_vlan":26,"public_mask":"27","public_gateway":"121.201.55.33","private_ip":"10.11.0.26","private_vlan":26,"private_mask":"24","private_gateway":"10.11.0.1"}', 'init', 2, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', ''),
(11, 'HJ-G001-P001', 'AddVMDisk', '', '2015-01-30 03:55:22', '{"vm_code":"A-08-509-26-20150130-001","disk_type":"iSCSI_SATA","disk_size":"50","vmdisk_id":3,"storage_code":"10.11.253.42_ISCSI_Sdc1"}', 'init', 2, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', ''),
(12, 'HJ-G001-P001', 'VMTemplateCreate', '', '2015-02-11 08:10:20', '{"template_code":"Template_CentOS_6.5_x86_64","cpu":"1","ram":"512","vm_code":"A-08-509-26-20150211-001"}', 'doing', 2, 1, '2015-02-11 08:10:25', '0000-00-00 00:00:00', 0, '', ''),
(13, 'HJ-G001-P001', 'VMNetworkConfig', '', '2015-02-11 08:10:20', '{"vm_code":"A-08-509-26-20150211-001","public_ip":"121.201.55.57","public_vlan":27,"public_mask":"27","public_gateway":"121.201.55.33","private_ip":"10.11.0.27","private_vlan":27,"private_mask":"24","private_gateway":"10.11.0.1"}', 'init', 2, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', ''),
(14, 'HJ-G001-P001', 'VMTemplateCreate', '', '2015-03-10 04:52:15', '{"template_code":"Template_CentOS_6.5_x86_64","cpu":"1","ram":"512","vm_code":"A-08-509-26-20150310-001"}', 'doing', 2, 1, '2015-03-10 04:52:17', '0000-00-00 00:00:00', 0, '', ''),
(15, 'HJ-G001-P001', 'VMNetworkConfig', '', '2015-03-10 04:52:15', 'false', 'init', 2, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '');

-- --------------------------------------------------------

--
-- 表的结构 `Template`
--

CREATE TABLE IF NOT EXISTS `Template` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `TemplateCode` varchar(255) NOT NULL,
  `SystemName` varchar(255) NOT NULL,
  `About` text NOT NULL,
  `State` varchar(50) NOT NULL DEFAULT 'enabled' COMMENT 'enabled -- 可用,  disabled -- 不可用',
  `StockHouseName` varchar(255) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- 转存表中的数据 `Template`
--

INSERT INTO `Template` (`ID`, `TemplateCode`, `SystemName`, `About`, `State`, `StockHouseName`) VALUES
(5, 'Template_CentOS_6.5_x86_64', 'centos_6.5_x64', '', 'enabled', '中山火炬');

-- --------------------------------------------------------

--
-- 表的结构 `VM`
--

CREATE TABLE IF NOT EXISTS `VM` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `PoolCode` char(200) NOT NULL,
  `HostCode` char(100) NOT NULL COMMENT '虚拟机所在物理机资产编号',
  `VMCode` char(100) NOT NULL,
  `Cpu` varchar(255) NOT NULL COMMENT '个',
  `Ram` varchar(255) NOT NULL COMMENT 'MB',
  `Disk` varchar(255) NOT NULL COMMENT 'GB',
  `TemplateCode` varchar(255) NOT NULL,
  `State` varchar(50) NOT NULL DEFAULT 'enabled' COMMENT 'enabled -- 可用,  disabled -- 不可用',
  `PowerState` char(50) NOT NULL,
  `TimeStamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `unique_pool_vm` (`PoolCode`,`VMCode`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=28 ;

--
-- 转存表中的数据 `VM`
--

INSERT INTO `VM` (`ID`, `PoolCode`, `HostCode`, `VMCode`, `Cpu`, `Ram`, `Disk`, `TemplateCode`, `State`, `PowerState`, `TimeStamp`) VALUES
(6, 'HJ-G001-P001', 'C-02-505-07-20110523-011', 'A-08-509-26-20150115-003', '4', '2048', '20', 'Template_CentOS_6.5_x86_64', 'enabled', 'Running', '2015-01-15 03:03:22'),
(5, 'HJ-G001-P001', 'A-02-505-04-20121126-005', 'A-08-509-26-20150115-002', '4', '2048', '20', 'Template_CentOS_6.5_x86_64', 'enabled', 'Running', '2015-01-15 03:03:25'),
(4, 'HJ-G001-P001', 'C-02-505-07-20110523-011', 'A-08-509-26-20150115-001', '4', '2048', '20', 'Template_CentOS_6.5_x86_64', 'enabled', 'Running', '2015-01-15 03:03:24'),
(7, 'HJ-G001-P001', '', 'A-08-509-26-20150115-004', '4', '2048', '20', 'Template_CentOS_6.5_x86_64', 'enabled', 'Halted', '2015-03-19 02:40:08'),
(8, 'HJ-G001-P001', 'A-02-505-04-20120429-021', 'A-08-509-26-20150115-005', '4', '2048', '20', 'Template_CentOS_6.5_x86_64', 'enabled', 'Running', '2015-03-19 02:40:11'),
(9, 'HJ-G001-P001', '', 'A-08-509-26-20150119-001', '4', '2048', '20', 'Template_CentOS_6.5_x86_64', 'disable', 'Halted', '2015-02-25 07:03:48'),
(10, 'HJ-G001-P001', '', 'A-08-509-26-20150119-002', '4', '2048', '20', 'Template_CentOS_6.5_x86_64', 'disable', 'Halted', '2015-02-25 07:03:50'),
(11, 'HJ-G001-P001', 'A-02-505-04-20120429-021', 'A-08-509-26-20150121-001', '4', '2048', '20', 'Template_CentOS_6.5_x86_64', 'enabled', 'Running', '2015-01-21 07:53:13'),
(12, 'HJ-G001-P001', 'A-02-505-04-20121126-005', 'A-08-509-26-20150121-002', '4', '2048', '20', 'Template_CentOS_6.5_x86_64', 'enabled', 'Running', '2015-02-25 07:45:48'),
(13, 'HJ-G001-P001', '', 'A-08-509-26-20150127-001', '4', '2048', '20', 'Template_CentOS_6.5_x86_64', 'enabled', 'Halted', '2015-01-27 09:18:01'),
(14, 'HJ-G001-P001', 'A-02-505-04-20120429-021', 'A-08-509-26-20150127-002', '4', '2048', '20', 'Template_CentOS_6.5_x86_64', 'enabled', 'Running', '2015-01-27 09:21:00'),
(15, 'HJ-G001-P001', '', 'A-08-509-26-20150127-003', '4', '2048', '20', 'Template_CentOS_6.5_x86_64', 'enabled', 'Halted', '2015-01-27 09:25:59'),
(16, 'HJ-G001-P001', 'A-02-505-04-20120429-021', 'A-08-509-26-20150127-004', '4', '2048', '20', 'Template_CentOS_6.5_x86_64', 'enabled', 'Running', '2015-01-27 09:29:04'),
(17, 'HJ-G001-P001', '', 'A-08-509-26-20150127-005', '4', '2048', '20', 'Template_CentOS_6.5_x86_64', 'enabled', 'Halted', '2015-01-27 09:37:04'),
(18, 'HJ-G001-P001', '', 'A-08-509-26-20150129-001', '4', '2048', '20', 'Template_CentOS_6.5_x86_64', 'enabled', 'Halted', '2015-01-29 01:32:57'),
(19, 'HJ-G001-P001', 'C-02-505-07-20110523-011', 'A-08-509-26-20150129-002', '4', '2048', '20', 'Template_CentOS_6.5_x86_64', 'enabled', 'Running', '2015-01-29 01:46:58'),
(20, 'HJ-G001-P001', '', 'A-08-509-26-20150129-003', '4', '2048', '20', 'Template_CentOS_6.5_x86_64', 'enabled', 'Halted', '2015-01-29 02:13:02'),
(21, 'HJ-G001-P001', '', 'A-08-509-26-20150129-004', '', '', '', 'Template_CentOS_6.5_x86_64', 'disable', '', '2015-01-29 01:56:16'),
(22, 'HJ-G001-P001', '', 'A-08-509-26-20150129-005', '4', '2048', '20', 'Template_CentOS_6.5_x86_64', 'enabled', 'Halted', '2015-01-29 02:13:00'),
(23, 'HJ-G001-P001', 'A-02-505-04-20121126-005', 'A-08-509-26-20150129-006', '4', '2048', '20', 'Template_CentOS_6.5_x86_64', 'enabled', 'Running', '2015-01-29 02:15:56'),
(24, 'HJ-G001-P001', '', 'A-08-509-26-20150129-007', '4', '2048', '20', 'Template_CentOS_6.5_x86_64', 'enabled', 'Halted', '2015-01-29 02:19:53'),
(25, 'HJ-G001-P001', '', 'A-08-509-26-20150130-001', '4', '2048', '20', 'Template_CentOS_6.5_x86_64', 'disabled', 'Halted', '2015-02-25 07:03:49'),
(26, 'HJ-G001-P001', '', 'A-08-509-26-20150211-001', '', '', '', 'Template_CentOS_6.5_x86_64', 'disabled', '', '2015-02-11 08:10:20'),
(27, 'HJ-G001-P001', '', 'A-08-509-26-20150310-001', '', '', '', 'Template_CentOS_6.5_x86_64', 'disabled', '', '2015-03-10 04:52:15');

-- --------------------------------------------------------

--
-- 表的结构 `VMDisk`
--

CREATE TABLE IF NOT EXISTS `VMDisk` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `VMCode` char(200) NOT NULL COMMENT '磁盘所属虚拟机',
  `DiskCode` char(200) NOT NULL COMMENT '磁盘唯一标识',
  `DiskType` char(50) NOT NULL COMMENT '磁盘类型 iSCSI|SATA|SSD|Local 等',
  `DiskSize` int(11) NOT NULL COMMENT '磁盘容量单位G',
  `State` char(50) NOT NULL COMMENT 'enabled -- 可用,  disabled -- 不可用',
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- 转存表中的数据 `VMDisk`
--

INSERT INTO `VMDisk` (`ID`, `VMCode`, `DiskCode`, `DiskType`, `DiskSize`, `State`) VALUES
(1, 'A-08-509-26-20150129-005', '', 'iSCSI_SATA', 10, 'enabled'),
(2, 'A-08-509-26-20150129-006', '', 'iSCSI_SATA', 10, 'enabled'),
(3, 'A-08-509-26-20150130-001', '', 'iSCSI_SATA', 50, 'disabled');

-- --------------------------------------------------------

--
-- 表的结构 `VMIP`
--

CREATE TABLE IF NOT EXISTS `VMIP` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `VMCode` varchar(255) NOT NULL,
  `IPAddr` varchar(255) NOT NULL,
  `Type` varchar(255) NOT NULL COMMENT 'Public代表公网 Private代表私网',
  `Vlan` varchar(255) NOT NULL,
  `State` char(50) NOT NULL DEFAULT 'enabled' COMMENT '标记是否可用',
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=59 ;

--
-- 转存表中的数据 `VMIP`
--

INSERT INTO `VMIP` (`ID`, `VMCode`, `IPAddr`, `Type`, `Vlan`, `State`) VALUES
(12, 'A-08-509-26-20150115-002', '10.11.0.4', 'Private', '4', 'enabled'),
(11, 'A-08-509-26-20150115-002', '121.201.55.34', 'Public', '4', 'enabled'),
(10, 'A-08-509-26-20150115-001', '10.11.0.3', 'Private', '3', 'init'),
(9, 'A-08-509-26-20150115-001', '121.201.55.33', 'Public', '3', 'init'),
(8, 'A-08-509-26-20150115-001', '10.11.0.2', 'Private', '2', 'enabled'),
(7, 'A-08-509-26-20150115-001', '121.201.55.32', 'Public', '2', 'enabled'),
(13, 'A-08-509-26-20150115-003', '121.201.55.35', 'Public', '5', 'enabled'),
(14, 'A-08-509-26-20150115-003', '10.11.0.5', 'Private', '5', 'enabled'),
(15, 'A-08-509-26-20150115-004', '121.201.55.36', 'Public', '6', 'enabled'),
(16, 'A-08-509-26-20150115-004', '10.11.0.6', 'Private', '6', 'enabled'),
(17, 'A-08-509-26-20150115-005', '121.201.55.37', 'Public', '7', 'enabled'),
(18, 'A-08-509-26-20150115-005', '10.11.0.7', 'Private', '7', 'enabled'),
(19, 'A-08-509-26-20150119-001', '121.201.55.38', 'Public', '8', 'init'),
(20, 'A-08-509-26-20150119-001', '10.11.0.8', 'Private', '8', 'init'),
(21, 'A-08-509-26-20150119-001', '121.201.55.39', 'Public', '9', 'init'),
(22, 'A-08-509-26-20150119-001', '10.11.0.9', 'Private', '9', 'init'),
(23, 'A-08-509-26-20150119-002', '121.201.55.40', 'Public', '10', 'init'),
(24, 'A-08-509-26-20150119-002', '10.11.0.10', 'Private', '10', 'init'),
(25, 'A-08-509-26-20150121-001', '121.201.55.41', 'Public', '11', 'enabled'),
(26, 'A-08-509-26-20150121-001', '10.11.0.11', 'Private', '11', 'enabled'),
(27, 'A-08-509-26-20150121-001', '121.201.55.42', 'Public', '12', 'init'),
(28, 'A-08-509-26-20150121-001', '10.11.0.12', 'Private', '12', 'init'),
(29, 'A-08-509-26-20150121-002', '121.201.55.43', 'Public', '13', 'enabled'),
(30, 'A-08-509-26-20150121-002', '10.11.0.13', 'Private', '13', 'enabled'),
(31, 'A-08-509-26-20150127-002', '121.201.55.44', 'Public', '14', 'enabled'),
(32, 'A-08-509-26-20150127-002', '10.11.0.14', 'Private', '14', 'enabled'),
(33, 'A-08-509-26-20150127-003', '121.201.55.45', 'Public', '15', 'enabled'),
(34, 'A-08-509-26-20150127-003', '10.11.0.15', 'Private', '15', 'enabled'),
(35, 'A-08-509-26-20150127-004', '121.201.55.46', 'Public', '16', 'enabled'),
(36, 'A-08-509-26-20150127-004', '10.11.0.16', 'Private', '16', 'enabled'),
(37, 'A-08-509-26-20150127-005', '121.201.55.47', 'Public', '17', 'enabled'),
(38, 'A-08-509-26-20150127-005', '10.11.0.17', 'Private', '17', 'enabled'),
(39, 'A-08-509-26-20150129-001', '121.201.55.48', 'Public', '18', 'enabled'),
(40, 'A-08-509-26-20150129-001', '10.11.0.18', 'Private', '18', 'enabled'),
(41, 'A-08-509-26-20150129-001', '121.201.55.49', 'Public', '19', 'init'),
(42, 'A-08-509-26-20150129-001', '10.11.0.19', 'Private', '19', 'init'),
(43, 'A-08-509-26-20150129-002', '121.201.55.50', 'Public', '20', 'enabled'),
(44, 'A-08-509-26-20150129-002', '10.11.0.20', 'Private', '20', 'enabled'),
(45, 'A-08-509-26-20150129-003', '121.201.55.51', 'Public', '21', 'enabled'),
(46, 'A-08-509-26-20150129-003', '10.11.0.21', 'Private', '21', 'enabled'),
(47, 'A-08-509-26-20150129-004', '121.201.55.52', 'Public', '22', 'init'),
(48, 'A-08-509-26-20150129-004', '10.11.0.22', 'Private', '22', 'init'),
(49, 'A-08-509-26-20150129-005', '121.201.55.53', 'Public', '23', 'enabled'),
(50, 'A-08-509-26-20150129-005', '10.11.0.23', 'Private', '23', 'enabled'),
(51, 'A-08-509-26-20150129-006', '121.201.55.54', 'Public', '24', 'enabled'),
(52, 'A-08-509-26-20150129-006', '10.11.0.24', 'Private', '24', 'enabled'),
(53, 'A-08-509-26-20150129-007', '121.201.55.55', 'Public', '25', 'enabled'),
(54, 'A-08-509-26-20150129-007', '10.11.0.25', 'Private', '25', 'enabled'),
(55, 'A-08-509-26-20150130-001', '121.201.55.56', 'Public', '26', 'init'),
(56, 'A-08-509-26-20150130-001', '10.11.0.26', 'Private', '26', 'init'),
(57, 'A-08-509-26-20150211-001', '121.201.55.57', 'Public', '27', 'init'),
(58, 'A-08-509-26-20150211-001', '10.11.0.27', 'Private', '27', 'init');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- phpMyAdmin SQL Dump
-- version 4.0.10.7
-- http://www.phpmyadmin.net
--
-- 主机: localhost
-- 生成日期: 2015-01-27 16:52:59
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=74 ;

--
-- 转存表中的数据 `Console`
--

INSERT INTO `Console` (`ID`, `CodeType`, `Code`, `VNCPort`, `TTY`, `TimeStamp`) VALUES
(71, 'VM', 'A-08-509-26-20150121-001', 5905, '/dev/pts/1', '2015-01-21 07:52:39'),
(70, 'VM', 'A-08-509-26-20150115-005', 5902, '/dev/pts/4', '2015-01-27 08:52:41'),
(69, 'VM', 'A-08-509-26-20150115-004', 5902, '/dev/pts/2', '2015-01-27 08:52:06'),
(68, 'VM', 'Transfer VM for VDI 6428c2fb-da4d-41af-8ece-46f9c8bf1865', 5906, '/dev/pts/8', '2015-01-27 08:52:41'),
(67, 'VM', 'Transfer VM for VDI 50e4e3ed-117f-4a92-a78d-de21ecc22ffd', 5905, '/dev/pts/7', '2015-01-27 08:52:41'),
(73, 'VM', 'squid', 5906, '/dev/pts/6', '2015-01-26 03:41:07'),
(72, 'VM', 'A-08-509-26-20150121-002', 5905, '/dev/pts/7', '2015-01-27 08:52:06'),
(66, 'VM', 'A-08-509-26-20150115-003', 5904, '/dev/pts/6', '2015-01-15 03:03:52'),
(65, 'VM', 'A-08-509-26-20150115-002', 5903, '/dev/pts/4', '2015-01-15 03:04:17'),
(64, 'VM', 'A-08-509-26-20150115-001', 5902, '/dev/pts/4', '2015-01-15 03:03:51'),
(63, 'VM', 'Template_CentOS_6.5_x86_64 (1)', 5903, '/dev/pts/5', '2015-01-27 08:52:41'),
(62, 'VM', 'Test_121.201.55.36', 5904, '/dev/pts/2', '2015-01-27 08:52:41'),
(61, 'VM', 'test_121.201.55.59', 5903, '/dev/pts/3', '2015-01-27 08:52:06'),
(60, 'VM', 'VM_Xen_Dev_Test', 5901, '/dev/pts/1', '2015-01-27 08:52:06'),
(59, 'VM', 'test_121.201.55.41', 5901, '/dev/pts/1', '2015-01-27 08:52:41'),
(58, 'VM', 'Test_121.201.55.37', 5903, '/dev/pts/2', '2015-01-27 08:52:31'),
(57, 'VM', 'Template_CentOS_6.5_x86_64 (2)', 5900, '/dev/pts/6', '2015-01-27 08:52:31'),
(56, 'VM', 'CentOS 6 (64-bit) (1)', 5902, '/dev/pts/3', '2015-01-27 08:52:30');

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=29 ;

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
(20, 'HJ-G001-P001', 'C-02-505-07-20110523-011', 'VMHost', 'enabled', '10.11.14.2', '10.11.14.1', '3000', '10.11.15.2', '10.11.15.1', '3200'),
(21, 'HJ-G001-P001', 'A-02-505-04-20120429-021', 'VMHost', 'enabled', '10.11.14.3', '10.11.14.1', '3000', '10.11.15.3', '10.11.15.1', '3200'),
(22, 'HJ-G001-P001', 'A-02-505-04-20121126-005', 'VMHost', 'enabled', '10.11.14.4', '10.11.14.1', '3000', '10.11.15.4', '10.11.15.1', '3200'),
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
  `PublicVlanBegin` varchar(255) NOT NULL,
  `PublicVlanEnd` varchar(255) NOT NULL,
  `PrivateVlanBegin` varchar(255) NOT NULL,
  `PrivateVlanEnd` varchar(255) NOT NULL,
  `PrivateIPBegin` varchar(255) NOT NULL,
  `PrivateIPEnd` varchar(255) NOT NULL,
  `PrivateMask` int(2) NOT NULL,
  `PrivateGateway` varchar(255) NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `unique_pool` (`PoolCode`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

--
-- 转存表中的数据 `Pool`
--

INSERT INTO `Pool` (`ID`, `GroupID`, `PoolCode`, `Data`, `State`, `PublicVlanBegin`, `PublicVlanEnd`, `PrivateVlanBegin`, `PrivateVlanEnd`, `PrivateIPBegin`, `PrivateIPEnd`, `PrivateMask`, `PrivateGateway`) VALUES
(7, 5, 'HJ-G001-P001', '{"master":"10.11.253.43","user":"root","pass":"Rjkj@efly#123","vnc_proxy_host":"121.201.55.35","vnc_proxy_port":"9000","uuid":"7106005d-e8e3-4da5-8adc-4e8035da77ad"}', 'enabled', '2', '201', '2', '201', '10.11.0.2', '10.11.0.254', 24, '10.11.0.1');

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=32 ;

--
-- 转存表中的数据 `PoolTask`
--

INSERT INTO `PoolTask` (`ID`, `PoolCode`, `Type`, `SubType`, `SubmitTime`, `Data`, `State`, `Try`, `Tried`, `StartTime`, `FinishTime`, `Ret`, `Result`, `Error`) VALUES
(13, 'HJ-G001-P001', 'VMTemplateCreate', '', '2015-01-15 03:11:03', '{"template_code":"Template_CentOS_6.5_x86_64","cpu":"4","ram":"2048","vm_code":"A-08-509-26-20150115-004"}', 'finish', 2, 1, '2015-01-15 03:11:17', '2015-01-15 03:11:38', 0, '', ''),
(12, 'HJ-G001-P001', 'VMNetworkConfig', '', '2015-01-15 02:33:40', '{"vm_code":"A-08-509-26-20150115-003","public_ip":"121.201.55.35","public_vlan":5,"public_mask":"27","public_gateway":"121.201.55.33","private_ip":"10.11.0.5","private_vlan":5,"private_mask":"24","private_gateway":"10.11.0.1"}', 'finish', 2, 0, '2015-01-15 02:34:51', '2015-01-15 02:34:54', 0, '', ''),
(11, 'HJ-G001-P001', 'VMTemplateCreate', '', '2015-01-15 02:33:40', '{"template_code":"Template_CentOS_6.5_x86_64","cpu":"4","ram":"2048","vm_code":"A-08-509-26-20150115-003"}', 'finish', 2, 1, '2015-01-15 02:34:17', '2015-01-15 02:34:29', 0, '', ''),
(9, 'HJ-G001-P001', 'VMTemplateCreate', '', '2015-01-15 02:13:06', '{"template_code":"Template_CentOS_6.5_x86_64","cpu":"4","ram":"2048","vm_code":"A-08-509-26-20150115-002"}', 'finish', 2, 1, '2015-01-15 02:13:21', '2015-01-15 02:13:42', 0, '', ''),
(10, 'HJ-G001-P001', 'VMNetworkConfig', '', '2015-01-15 02:13:06', '{"vm_code":"A-08-509-26-20150115-002","public_ip":"121.201.55.34","public_vlan":4,"public_mask":"27","public_gateway":"121.201.55.33","private_ip":"10.11.0.4","private_vlan":4,"private_mask":"24","private_gateway":"10.11.0.1"}', 'finish', 2, 0, '2015-01-15 02:32:17', '2015-01-15 02:32:21', 0, '', ''),
(7, 'HJ-G001-P001', 'VMTemplateCreate', '', '2015-01-15 01:44:56', '{"template_code":"Template_CentOS_6.5_x86_64","cpu":"4","ram":"2048","vm_code":"A-08-509-26-20150115-001"}', 'finish', 2, 1, '2015-01-15 01:45:17', '2015-01-15 01:45:39', 0, '', ''),
(8, 'HJ-G001-P001', 'VMNetworkConfig', '', '2015-01-15 01:44:56', '{"vm_code":"A-08-509-26-20150115-001","public_ip":"121.201.55.32","public_vlan":"2","public_mask":"27","public_gateway":"121.201.55.33","private_ip":"10.11.0.2","private_vlan":"2","private_mask":"24","private_gateway":"10.11.0.1"}', 'finish', 2, 0, '2015-01-15 02:02:51', '2015-01-15 02:04:52', 0, '', ''),
(14, 'HJ-G001-P001', 'VMNetworkConfig', '', '2015-01-15 03:11:03', '{"vm_code":"A-08-509-26-20150115-004","public_ip":"121.201.55.36","public_vlan":6,"public_mask":"27","public_gateway":"121.201.55.33","private_ip":"10.11.0.6","private_vlan":6,"private_mask":"24","private_gateway":"10.11.0.1"}', 'finish', 2, 0, '2015-01-15 03:13:16', '2015-01-15 03:13:20', 0, '', ''),
(15, 'HJ-G001-P001', 'VMTemplateCreate', '', '2015-01-15 03:11:07', '{"template_code":"Template_CentOS_6.5_x86_64","cpu":"4","ram":"2048","vm_code":"A-08-509-26-20150115-005"}', 'finish', 2, 1, '2015-01-15 03:12:17', '2015-01-15 03:12:38', 0, '', ''),
(16, 'HJ-G001-P001', 'VMNetworkConfig', '', '2015-01-15 03:11:07', '{"vm_code":"A-08-509-26-20150115-005","public_ip":"121.201.55.37","public_vlan":7,"public_mask":"27","public_gateway":"121.201.55.33","private_ip":"10.11.0.7","private_vlan":7,"private_mask":"24","private_gateway":"10.11.0.1"}', 'finish', 2, 0, '2015-01-15 03:14:16', '2015-01-15 03:14:20', 0, '', ''),
(17, 'HJ-G001-P001', 'VMTemplateCreate', '', '2015-01-19 02:34:16', '{"template_code":"Template_CentOS_6.5_x86_64","cpu":"1","ram":"512","vm_code":"A-08-509-26-20150119-001"}', 'doing', 2, 1, '2015-01-19 02:35:12', '0000-00-00 00:00:00', 0, '', ''),
(18, 'HJ-G001-P001', 'VMNetworkConfig', '', '2015-01-19 02:34:16', '{"vm_code":"A-08-509-26-20150119-001","public_ip":"121.201.55.38","public_vlan":8,"public_mask":"27","public_gateway":"121.201.55.33","private_ip":"10.11.0.8","private_vlan":8,"private_mask":"24","private_gateway":"10.11.0.1"}', 'init', 2, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', ''),
(19, 'HJ-G001-P001', 'VMTemplateCreate', '', '2015-01-19 02:35:47', '{"template_code":"Template_CentOS_6.5_x86_64","cpu":"1","ram":"512","vm_code":"A-08-509-26-20150119-002"}', 'doing', 2, 1, '2015-01-19 02:36:08', '0000-00-00 00:00:00', 0, '', ''),
(20, 'HJ-G001-P001', 'VMNetworkConfig', '', '2015-01-19 02:35:47', '{"vm_code":"A-08-509-26-20150119-002","public_ip":"121.201.55.40","public_vlan":10,"public_mask":"27","public_gateway":"121.201.55.33","private_ip":"10.11.0.10","private_vlan":10,"private_mask":"24","private_gateway":"10.11.0.1"}', 'init', 2, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', ''),
(21, 'HJ-G001-P001', 'VMTemplateCreate', '', '2015-01-21 07:33:18', '{"template_code":"Template_CentOS_6.5_x86_64","cpu":"4","ram":"2048","vm_code":"A-08-509-26-20150121-001"}', 'finish', 2, 1, '2015-01-21 07:34:08', '2015-01-21 07:34:31', 0, '', ''),
(22, 'HJ-G001-P001', 'VMNetworkConfig', '', '2015-01-21 07:33:18', '{"vm_code":"A-08-509-26-20150121-001","public_ip":"121.201.55.41","public_vlan":11,"public_mask":"27","public_gateway":"121.201.55.33","private_ip":"10.11.0.11","private_vlan":11,"private_mask":"24","private_gateway":"10.11.0.1"}', 'finish', 2, 0, '2015-01-21 07:50:38', '2015-01-21 07:50:42', 0, '', ''),
(23, 'HJ-G001-P001', 'VMTemplateCreate', '', '2015-01-21 07:54:02', '{"template_code":"Template_CentOS_6.5_x86_64","cpu":"4","ram":"2048","vm_code":"A-08-509-26-20150121-002"}', 'finish', 2, 1, '2015-01-21 07:54:08', '2015-01-21 07:54:30', 0, '', ''),
(24, 'HJ-G001-P001', 'VMNetworkConfig', '', '2015-01-21 07:54:02', '{"vm_code":"A-08-509-26-20150121-002","public_ip":"121.201.55.43","public_vlan":13,"public_mask":"27","public_gateway":"121.201.55.33","private_ip":"10.11.0.13","private_vlan":13,"private_mask":"24","private_gateway":"10.11.0.1"}', 'finish', 2, 0, '2015-01-21 07:56:06', '2015-01-21 07:56:11', 0, '', ''),
(31, 'HJ-G001-P001', 'AddVMDisk', '', '2015-01-27 08:24:32', '{"vm_code":"A-08-509-26-20150115-005","disk_type":"iSCSI","disk_size":"10","vmdisk_id":2,"storage_code":"10.11.253.42_ISCSI_Sdc1"}', 'finish', 2, 2, '2015-01-27 08:47:03', '2015-01-27 08:47:07', 0, '', '');

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=13 ;

--
-- 转存表中的数据 `VM`
--

INSERT INTO `VM` (`ID`, `PoolCode`, `HostCode`, `VMCode`, `Cpu`, `Ram`, `Disk`, `TemplateCode`, `State`, `PowerState`, `TimeStamp`) VALUES
(6, 'HJ-G001-P001', 'C-02-505-07-20110523-011', 'A-08-509-26-20150115-003', '4', '2048', '20', 'Template_CentOS_6.5_x86_64', 'enabled', 'Running', '2015-01-15 03:03:22'),
(5, 'HJ-G001-P001', 'A-02-505-04-20121126-005', 'A-08-509-26-20150115-002', '4', '2048', '20', 'Template_CentOS_6.5_x86_64', 'enabled', 'Running', '2015-01-15 03:03:25'),
(4, 'HJ-G001-P001', 'C-02-505-07-20110523-011', 'A-08-509-26-20150115-001', '4', '2048', '20', 'Template_CentOS_6.5_x86_64', 'enabled', 'Running', '2015-01-15 03:03:24'),
(7, 'HJ-G001-P001', 'A-02-505-04-20121126-005', 'A-08-509-26-20150115-004', '4', '2048', '20', 'Template_CentOS_6.5_x86_64', 'enabled', 'Running', '2015-01-27 08:52:01'),
(8, 'HJ-G001-P001', 'C-02-505-07-20110523-011', 'A-08-509-26-20150115-005', '4', '2048', '20', 'Template_CentOS_6.5_x86_64', 'enabled', 'Running', '2015-01-27 08:52:04'),
(9, 'HJ-G001-P001', '', 'A-08-509-26-20150119-001', '4', '2048', '20', 'Template_CentOS_6.5_x86_64', 'disable', 'Halted', '2015-01-27 08:52:58'),
(10, 'HJ-G001-P001', '', 'A-08-509-26-20150119-002', '4', '2048', '20', 'Template_CentOS_6.5_x86_64', 'disable', 'Halted', '2015-01-27 08:52:59'),
(11, 'HJ-G001-P001', 'A-02-505-04-20120429-021', 'A-08-509-26-20150121-001', '4', '2048', '20', 'Template_CentOS_6.5_x86_64', 'enabled', 'Running', '2015-01-21 07:53:13'),
(12, 'HJ-G001-P001', 'A-02-505-04-20121126-005', 'A-08-509-26-20150121-002', '4', '2048', '20', 'Template_CentOS_6.5_x86_64', 'enabled', 'Running', '2015-01-27 08:52:58');

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=31 ;

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
(30, 'A-08-509-26-20150121-002', '10.11.0.13', 'Private', '13', 'enabled');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

#!/usr/bin/python
# encoding:utf-8

import urllib
import urllib2
import time
import xml.dom.minidom
import logging
import logging.config
import json
import os
import sys
import hashlib
import re

reload(sys)
sys.setdefaultencoding('utf8')
os.chdir(sys.path[0])


error = 0
logging.config.fileConfig("logging.conf")    # 采用配置文件
logger = logging.getLogger()

# 自定义的异常类
class NetworkError(Exception):
	tp = 0
	name = ""
	def __init__(self, arg1, arg2):
		self.tp = arg1
		self.name = arg2

ErrorType = {0:'Database Connection Error',1:'Database Query Error',2:'Database Modification Error',
             3:'File Deletion Error',4:'File Modification Error',5:'IP Conflict Error',6:'Invalid FIB Error',7:'Invalid Info File Error'}

# 显示提示信息的函数
def warning(errortype):
	global error
	logger.error("Configuration Error: Parameter " + errortype + "is not set or is not set properly.")
	error += 1

# 获得各项设置的函数
def get(type):
	global conf,error
	tmp = conf.getElementsByTagName(type)
	if len(tmp) <= 0 or len(tmp[0].childNodes) <= 0:
		warning(type)
		return ""
	else:
		return tmp[0].childNodes[0].data

# **************获取配置参数*******************
def getconf():
	global DOMTree,conf,location,ip,longitude,latitude,monitor,receive_FIB_path,send_FIB_path,remark,password
	# 使用minidom解析器打开 XML 文档
	DOMTree = xml.dom.minidom.parse("conf.xml")
	conf = DOMTree.documentElement

	# 获取Location
	location = get("Location")

	# 获取IP
	ip = get("IP")

	# 获取经度
	longitude = get("Longitude")

	# 获取纬度
	latitude = get("Latitude")

	# 获取监测站点的url
	monitor = "http://" + get("Monitor_Host")

	# 获取Receive_FIB_path
	receive_FIB_path = get("Receive_FIB_path")

	# 获取Send_FIB_path
	send_FIB_path = get("Send_FIB_path")

	# 获取Remark
	remark = get("Remark")

	# 获取Password
	m = hashlib.md5()
	m.update(get("Password"))
	password = m.hexdigest()
# **************获取配置参数*******************

# **************获取系统参数*******************
def getsystem():
	global mem, cpu, net, uptime, hd
	# Memory
	mem = {}
	f = open("/proc/meminfo")
	lines = f.readlines()
	f.close()
	for line in lines:
		if len(line) < 2: continue
		name = line.split(':')[0]
		var = line.split(':')[1].split()[0]
		mem[name] = long(var) * 1024.0
	mem['MemUsed'] = mem['MemTotal'] - mem['MemFree'] - mem['Buffers'] - mem['Cached']

	# CPU
	cpu = []
	cpuinfo = {}
	f = open("/proc/cpuinfo")
	lines = f.readlines()
	f.close()
	for line in lines:
		if line == '\n':
			cpu.append(cpuinfo)
			cpuinfo = {}
		if len(line) < 2: continue
		name = line.split(':')[0].rstrip()
		var = line.split(':')[1]
		cpuinfo[name] = var

	# uptime
	uptime = {}
	f = open("/proc/uptime")
	con = f.read().split()
	f.close()
	all_sec = float(con[0])
	MINUTE,HOUR,DAY = 60,3600,86400
	uptime['day'] = int(all_sec / DAY )
	uptime['hour'] = int((all_sec % DAY) / HOUR)
	uptime['minute'] = int((all_sec % HOUR) / MINUTE)
	uptime['second'] = int(all_sec % MINUTE)
	uptime['Free rate'] = float(con[1]) / float(con[0])

	# network
	net = []
	f = open("/proc/net/dev")
	lines = f.readlines()
	f.close()
	for line in lines[2:]:
		con = line.split()
		"""
		intf = {}
		intf['interface'] = con[0].lstrip(":")
		intf['ReceiveBytes'] = int(con[1])
		intf['ReceivePackets'] = int(con[2])
		intf['ReceiveErrs'] = int(con[3])
		intf['ReceiveDrop'] = int(con[4])
		intf['ReceiveFifo'] = int(con[5])
		intf['ReceiveFrames'] = int(con[6])
		intf['ReceiveCompressed'] = int(con[7])
		intf['ReceiveMulticast'] = int(con[8])
		intf['TransmitBytes'] = int(con[9])
		intf['TransmitPackets'] = int(con[10])
		intf['TransmitErrs'] = int(con[11])
		intf['TransmitDrop'] = int(con[12])
		intf['TransmitFifo'] = int(con[13])
		intf['TransmitFrames'] = int(con[14])
		intf['TransmitCompressed'] = int(con[15])
		intf['TransmitMulticast'] = int(con[16])
		"""
		intf = dict(
			zip(
				( 'interface','ReceiveBytes','ReceivePackets',
				  'ReceiveErrs','ReceiveDrop','ReceiveFifo',
				  'ReceiveFrames','ReceiveCompressed','ReceiveMulticast',
				  'TransmitBytes','TransmitPackets','TransmitErrs',
				  'TransmitDrop', 'TransmitFifo','TransmitFrames',
				  'TransmitCompressed','TransmitMulticast' ),
				( con[0].rstrip(":"),int(con[1]),int(con[2]),
				  int(con[3]),int(con[4]),int(con[5]),
				  int(con[6]),int(con[7]),int(con[8]),
				  int(con[9]),int(con[10]),int(con[11]),
				  int(con[12]),int(con[13]),int(con[14]),
				  int(con[15]),int(con[16]), )
			)
		)

		net.append(intf)

	# Disk
	hd = {}
	disk = os.statvfs("/")
	hd['available'] = disk.f_bsize * disk.f_bavail
	hd['capacity'] = disk.f_bsize * disk.f_blocks
	hd['used'] = disk.f_bsize * disk.f_bfree

# **************获取系统参数*******************


def sendpost(url, data, save_location=''):
	data_urlencode = urllib.urlencode(data)
	req = urllib2.Request(url=url, data=data_urlencode)
	# print req  # 若不想打印信息可以注释此行
	res_data = urllib2.urlopen(req)
	res = res_data.read()
	if save_location != '':
		with open(save_location, "wb") as code:
			code.write(res)
	else:
		fail=json.loads(res)
		if fail['Fail'] != 0:
			raise NetworkError(fail['Type'],os.path.basename(url))

while True:
	time.sleep(2)  # time

	try:
		getconf()

		if error == 0:
			#  发给alive的部分
			alive_data = {'event': 'sendalive'}
			alive_data['ip'] = ip  # 发给alive.php的IP地址
			alive_data['longitude'] = longitude
			alive_data['latitude'] = latitude
			alive_data['location'] = location  # 发给alive.php的Location
			alive_data['remark'] = remark
			alive_data['password'] = password
			getsystem()
			alive_data['system'] = str(cpu)+'\n'+str(mem)+'\n'+str(hd)+'\n'+str(net)+'\n'+str(uptime)+'\n'
			alive_url = monitor + "/alive.php"  # alive_url里存的是alive.php的URL
			sendpost(alive_url, alive_data)

			# 发给fibdown的部分
			fibdown_data = {'event': 'sendfibdown'}
			fibdown_data['ip'] = ip  # 发给fibdown.php的IP地址
			fibdown_url = monitor + "/FIBdown.php"  # fibdown_url里存的是FIBdown.php的URL
			save_location = receive_FIB_path  # save_location里存的是接收到的FIB表的存放路径和存放文件名
			sendpost(fibdown_url, fibdown_data, save_location)

			# 发给fibup的部分
			fib_location = send_FIB_path  # fib_location里存的是要上传的FIB表的位置
			fibup_url = monitor + "/FIBup.php"  # fib_url里存的是FIBup.php的URL
			with open(fib_location) as fib:
				fibup_data = {'event': 'sendfibup', 'file': fib.read(), 'ip': ip}
			sendpost(fibup_url, fibup_data)
	except NetworkError, e:
		logger.error('PHP Feedback Error: In '+str(e.name)+": "+ErrorType[e.tp])
		continue
	except urllib2.HTTPError, e:
		logger.error('HTTP Error[%s]' % str(e.code))
		continue
	except urllib2.URLError, e:
		logger.error('URL Error: '+str(e.reason))
		continue
	except xml.dom.DOMException, e:
		logger.error('XML Error:'+str(e))
		continue
	except IOError, e:
		logger.error('File Error:'+str(e))
		continue
	except KeyboardInterrupt:
		break
	except Exception,e:
		logger.error('Other Error: '+str(e))
		continue

#!/usr/bin/python
# encoding:utf-8

import urllib
import urllib2
import os
import sys
import logging
import logging.config
import json

reload(sys)
sys.setdefaultencoding('utf8')
os.chdir(sys.path[0])
logging.config.fileConfig("logging_monitor.conf")    # 采用配置文件
logger = logging.getLogger('clean')

# 自定义的异常类
class NetworkError(Exception):
	tp = 0
	name = ""
	def __init__(self, arg1, arg2):
		self.tp = arg1
		self.name = arg2

ErrorType = {0:'Database Connection Error',1:'Database Query Error',2:'Database Modification Error',
             3:'File Deletion Error',4:'File Modification Error',5:'IP Conflict Error',6:'Invalid FIB Error',7:'Invalid Info File Error'}

def sendpost(url, data):
	data_urlencode = urllib.urlencode(data)
	req = urllib2.Request(url=url, data=data_urlencode)
	res_data = urllib2.urlopen(req)
	res = res_data.read()
	fail = json.loads(res)
	if fail['Fail'] == 1:
		raise NetworkError(fail['Type'],os.path.basename(url))
	elif fail['Fail'] == 0:
		logger.info(fail['content'])
	return

now=0
total=10

#  发给clean.php的部分
while True:
	try:
		now = now + 1
		alive_data = {'event': 'clean','id':-1}
		alive_url = "http://localhost/clean.php"
		sendpost(alive_url, alive_data)
		break
	except NetworkError, e:
		logger.error('In '+str(e.name)+": "+ErrorType[e.tp])
		if now >=total:
			logger.critical('Fail to clean, have retried %d times' % total)
			break
		else:
			continue
	except urllib2.HTTPError, e:
		logger.error('HTTP Error[%s]' % str(e.code))
		if now >=total:
			logger.critical('Fail to clean, have retried %d times' % total)
			break
		else:
			continue
	except urllib2.URLError, e:
		logger.error('URL Error: '+str(e.reason))
		if now >=total:
			logger.critical('Fail to clean, have retried %d times' % total)
			break
		else:
			continue
	except KeyboardInterrupt:
		logger.warning('Interrupt by Keyboard [CTRL + C]')
		break
	except Exception,e:
		logger.error('Other Error: '+str(e))
		if now >=total:
			logger.critical('Fail to clean, have retried %d times' % total)
			break
		else:
			continue


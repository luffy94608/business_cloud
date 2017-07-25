#!/usr/bin/python
# -*- coding: UTF-8 -*-

import os,sys
import re
import time

#项目更新脚本
#timestamp = int(time.time())
timestamp = time.strftime("%Y%m%d%H%M%S", time.localtime())
print "version："+timestamp

#替换文字字符换函数
def replaceFileData(filename, reg, str):
    data2=''
    if(os.path.exists(filename)):
        file = open(filename,'r+')
        data = file.read()
        data2 = re.sub(reg, str, data)
        file.flush()
        file.close()

    open(filename,'w').write(data2)

#更新代码
os.system('git pull')

#混淆代码
os.system('node ./public/bower_components/r.js/dist/r.js -o ./public/build.js')

#替换 mainfest 缓存资源 版本号
replaceFileData('./public/wechat.appcache', r'=([\d]+)', "=%s" % (timestamp))
print 'file:wecchat.appcache update success'

#替换 env 缓存 版本号
replaceFileData('./.env', r'VERSION_CODE=([\d]+)', "VERSION_CODE=%s" % (timestamp))
print 'file:.env update success'


print 'all is over!!!'


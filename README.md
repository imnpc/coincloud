## 环境要求

### 基本要求: PHP 8.1 + MySql 5.7 + Redis

Composer

PHP >= 8.1

MySql > 5.7

Zip PHP Extension

OpenSSL PHP Extension

PDOMysql PHP Extension

Mbstring PHP Extension

Tokenizer PHP Extension

XML PHP Extension

Fileinfo PHP Extension

Redis PHP Extension

## 安装步骤

### 1.安装宝塔面板最新版,修改PHP配置

```bash
yum install -y wget && wget -O install.sh http://download.bt.cn/install/install_6.0.sh && sh install.sh
```

安装完毕以后登录到面板 选择 Nginx 1.18 + PHP 8.1 + MySql 5.7 进行安装,安装完毕前往 软件商店->运行环境,安装 Redis.

php配置:软件商店->运行环境->PHP 8.1->设置,

安装扩展->安装扩展 fileinfo opcache redis exif intl.

禁用函数->需要删除的屏蔽函数 putenv proc_open symlink pcntl_signal pcntl_signal_dispatch pcntl_alarm

### 2.建立网站和配置

新建网站,然后将代码压缩包上传到网站根目录,解压.

网站目录->关闭 防跨站攻击

网站目录->运行目录->/public->保存

伪静态->选择 laravel5

SSL->按照需求申请一个SSL证书,推荐 Let's Encrypt 免费证书 不要开启强制 HTTPS


### 3.env 文件配置

复制根目录的 .env.example 文件,改名为 .env，

请务必配置好数据库和 OSS 相关资料

修改以下选项(中文或者带空格请用双引号引入,例如 APP_NAME="My Site" )

APP_NAME=website

APP_URL=https://demo.com

DB_DATABASE=数据库名称

DB_USERNAME=数据库帐号

DB_PASSWORD=数据库密码

OSS_ACCESS_KEY_ID=阿里云ACCESS_KEY

OSS_ACCESS_KEY_SECRET=阿里云ACCESS_SECRET

OSS_ENDPOINT=OSS节点

OSS_BUCKET=OSS存储名

OSS_DOMAIN=OSS绑定自有域名访问URL

FILESYSTEM_DISK=oss

ALIYUN_SMS_SIGN_NAME=阿里云短信签名

ALIYUN_SMS_TEMPLATE=阿里云短信模版

配置完毕以后访问后台: 网站地址/admin

帐号 admin

密码 admin

### 4.初始化参数和计划任务和守护进程
例如网站是 test.demo.com

#### (1)ssh登录服务器

执行初始化命令
```bash
cd /www/wwwroot/test.demo.com/

php artisan migrate

php artisan key:generate

php artisan storage:link

chmod -R  0777 storage
```
新增计划任务
```bash
crontab -u www -e
```
计划任务内容
```bash
*/1 * * * * /www/server/php/81/bin/php /www/wwwroot/test.demo.com/artisan schedule:run >> /www/wwwroot/test.demo.com/storage/logs/cron.log 2>&1
```
然后执行以下命令,查看是否生效
```bash
crontab -u www -l
```
#### (2)守护进程
宝塔后台,软件商店->系统工具->安装 Supervisor管理器,安装完毕点击打开 Supervisor管理器.

添加守护进程

->名称:cloud

->启动用户:www

->运行目录:选择当前网站根目录 /www/wwwroot/test.demo.com/

->启动命令:/www/server/php/81/bin/php /www/wwwroot/test.demo.com/artisan horizon

然后保存即可



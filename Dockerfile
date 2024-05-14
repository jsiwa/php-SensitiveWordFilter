# 使用官方PHP 7.4 CLI镜像
FROM php:7.4.33-cli

# 设置工作目录
WORKDIR /app

# 将当前目录的所有文件复制到容器的 /app 目录
COPY . /app

# 配置PHP以显示所有错误
RUN echo "display_errors=On" >> /usr/local/etc/php/php.ini
RUN echo "error_reporting=E_ALL" >> /usr/local/etc/php/php.ini

# 执行PHP脚本的命令
CMD ["php", "/app/test.php"]

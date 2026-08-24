FROM php:8.2-apache

# تثبيت الحزم المطلوبة لـ Laravel
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

# تثبيت Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# ضبط مجلد العمل
WORKDIR /var/www/html

# نسخ ملفات المشروع
COPY . .

# إعطاء صلاحيات التخزين وแคش
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# توجيه الـ Apache إلى مجلد public الخاص بـ لاراول
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# تفعيل mod_rewrite الخاص بلاراول
RUN a2enmod rewrite

# تثبيت حزم PHP عبر Composer
RUN composer install --no-dev --optimize-autoloader

EXPOSE 80
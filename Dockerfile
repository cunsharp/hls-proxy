FROM php:8.2-apache

# Install required extensions
RUN apt-get update && apt-get install -y \
    libcurl4-openssl-dev \
    && docker-php-ext-install curl \
    && a2enmod rewrite

# Copy PHP files
COPY index.php /var/www/html/

# Set permissions
RUN chown -R www-data:www-data /var/www/html

# Expose port
EXPOSE 10000

# Update Apache to listen on PORT from environment
RUN sed -i 's/Listen 80/Listen ${PORT}/' /etc/apache2/ports.conf
RUN sed -i 's/:80/:${PORT}/' /etc/apache2/sites-available/000-default.conf

CMD ["apache2-foreground"]
```

4. Click **"Commit changes"**

---

### **STEP 6: Deploy on Render**

1. Go to **https://render.com**
2. Click **"Get Started"** or **"Sign Up"**
3. Sign up with **GitHub** (easiest option)
4. Authorize Render to access your GitHub
5. Click **"New +"** → **"Web Service"**
6. Find your `hls-proxy` repository and click **"Connect"**
7. Fill in:
   - **Name:** `hls-proxy` (or any name you want)
   - **Environment:** Docker
   - **Plan:** Free
8. Click **"Create Web Service"**

---

### **STEP 7: Wait for Deployment**

- Render will build and deploy your service
- This takes **5-10 minutes** for the first time
- You'll see logs showing the build progress
- Once it says **"Live"**, it's ready! ✅

---

### **STEP 8: Get Your URL**

Once deployed, you'll get a URL like:
```
https://hls-proxy-xxxx.onrender.com
```

Your proxy endpoint will be:
```
https://hls-proxy-xxxx.onrender.com/index.php
```

---

### **STEP 9: Test Your Proxy**

Test with this URL in your browser:
```
https://hls-proxy-xxxx.onrender.com/index.php?url=http://commondatastorage.googleapis.com/gtv-videos-bucket/sample/BigBuckBunny.mp4

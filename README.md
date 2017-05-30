# jupiter-wp-template
Standard Jupiter Themed Wordpress

## front end developer begin:
Are you a new developer to the company? If so, following the instruction guide here:
- http://wiki.jellyfish.tmp/index.php/Sass_Set_Up_on_a_Dev_Container

## environment configuration:
```bash
cd /home/sites
git clone git@github.com:{USER}/{website name}.git
cd /home/sites/{website name}
git remote add upstream git@github.com:JellyfishGroup/{website name}.git
git remote set-url upstream --push no-pushing
git remote -v
chown apache:apache -R /home/sites/{website name}/public_html/
```

## task runner set-up:
```bash
cd /home/sites/{website name}/task_runner
npm install
gulp watch
```

## task runner packages:
```bash
cd /home/sites/{website name}/task_runner
npm install es6-promise
npm install gulp-sourcemaps
npm install gulp-sass
npm install gulp-notify
npm install gulp-plumber
npm install gulp-rename
npm install gulp-postcss
npm install autoprefixer
npm install postcss-svg-fragments
npm install cssnano
npm install gulp-concat
npm install gulp-uglify
npm install run-sequence
npm install del
```

## .ini configuration:
```bash
cd /home/sites/{website name}
cp install/{website name}.ini /etc/jellyfish/
nano /etc/jellyfish/{website name}.ini
service httpd restart
```

## php customs:
Files that define how elements are parsed and painted within the DOM. This file is similar to a `functions.php` file of a WordPress environment.
```bash
nano /home/sites/{website name}/public_html/wp-content/themes/jupiter-child/functions.php
```

## templates location:
Files that define individual construct elements of a page (parts, includes, globals, etc.).
```bash
cd /home/sites/{website name}/public_html/wp-content/themes/jupiter-child/*.php
cd /home/sites/{website name}/public_html/wp-content/themes/jupiter-child/template-inc/*.php
cd /home/sites/{website name}/public_html/wp-content/themes/jupiter-child/template-parts/*.php
```

## WP CLI install:
```wp core install --allow-root --url='dev_wp_sandbox.sgel6.dev.jellyfish.local' --admin_user='admin' --admin_password='J3llyf!sh' --admin_email='steven.gallagher@jellyfish.net' --title='Wordpress Sandbox'
```
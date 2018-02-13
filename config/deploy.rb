lock "3.9.1"

set :department, 'cdrs'
set :application, 'researchblogs'
set :remote_user, "#{fetch(:department)}serv"
set :repo_url, "git@github.com:cul/#{fetch(:department)}-#{fetch(:application)}.git"
set :deploy_to, "/opt/www/#{fetch(:department)}/#{fetch(:application)}_#{fetch(:stage)}"
set :ssh_options, { :forward_agent => true }

set :keep_releases, 2

######################
# WordPress settings #
######################

# Set up important directories
set :wp_docroot, "#{fetch(:deploy_to)}/html"
set :wp_content_path, "#{fetch(:deploy_to)}/wp-content"
set :multisite, true
set :title, 'Research Blogs'

# List custom plugins and themes to pull in from repo

set :wp_custom_mu_plugins, {
  'wind_plugin' => 'wp-content/mu-plugins/wind_plugin',
  'wind.php' => 'wp-content/mu-plugins/wind.php',
  'alter-kses' => 'wp-content/mu-plugins/alter-kses',
  'cache-flusher' => 'wp-content/mu-plugins/cache-flusher',
  'listem' => 'wp-content/mu-plugins/listem',
  'more-privacy' => 'wp-content/mu-plugins/more-privacy',
  'push-updates' => 'wp-content/mu-plugins/push-updates',
  'show_emails' => 'wp-content/mu-plugins/show_emails',
  'user-switching' => 'wp-content/mu-plugins/user-switching'
}

set :wp_custom_themes, {
  'twentyeleven' => 'wp-content/themes/twentyeleven'
}

# WTAH Voting Buttons

A simple and effective WordPress plugin to gather user feedback with Yes/No voting buttons on articles.

## Installation

1. Clone the repository in your `wp-content/plugins` directory, or download the zip file and install it through the WordPress plugins screen.
2. Navigate to the WordPress plugins screen and activate WTAH Voting Buttons.

## Usage

Once activated, the plugin automatically adds Yes/No voting buttons to the bottom of each post. 
Customize the appearance and functionality through the settings page under the WordPress admin panel.

## Features

- Simple Yes/No voting
- Live percentage feedback
- Customizable placement of feature
- Pause feature side wide or under each post
- Information stored in admin columns and in posts as metabox

## File Structure
was-this-article-helpful/
┣ admin/
┃ ┣ admin-columns.php
┃ ┣ metabox.php
┃ ┗ settings.php
┣ includes/
┃ ┣ functions.php
┃ ┗ helpers.php
┣ public/
┃ ┣ css/
┃ ┃ ┣ style.css
┃ ┃ ┣ style.css.map
┃ ┃ ┗ style.scss
┃ ┣ icons/
┃ ┃ ┣ face-meh-solid.svg
┃ ┃ ┗ face-smile-solid.svg
┃ ┗ js/
┃   ┣ ajax.js
┃   ┗ script.js
┣ readme.md
┗ was-this-article-helpful.php
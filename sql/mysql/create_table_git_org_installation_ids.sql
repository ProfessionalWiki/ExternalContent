CREATE TABLE /*_*/git_org_installation_ids (
     goi_id INT(10) UNSIGNED PRIMARY KEY AUTO_INCREMENT,
     organisation_name VARCHAR(255) NOT NULL,
     github_app_installation_id INT(10) UNSIGNED NOT NULL,
     UNIQUE KEY unique_org_installation (organisation_name, github_app_installation_id),
     INDEX goidinx_github_app_installation_id (github_app_installation_id),
     INDEX goidinx_orgname (organisation_name)
) /*$wgDBTableOptions*/;

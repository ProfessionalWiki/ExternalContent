CREATE TABLE /*_*/git_access_tokens (
    gat_id INT(10) UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    goi_id INT(10) UNSIGNED NOT NULL,
    gat_github_access_token VARCHAR(255),
    gat_created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX gatinx_orgname (gat_github_access_token),
    INDEX gatinx_created_at (gat_created_at),
    CONSTRAINT fk_git_access_tokens_goi 
        FOREIGN KEY (goi_id) 
        REFERENCES /*_*/git_org_installation_ids(goi_id) 
        ON DELETE CASCADE
)/*$wgDBTableOptions*/;
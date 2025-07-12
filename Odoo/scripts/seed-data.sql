-- Insert sample skills
INSERT INTO skills (name, category) VALUES
('JavaScript', 'Programming'),
('Python', 'Programming'),
('PHP', 'Programming'),
('HTML/CSS', 'Web Development'),
('React', 'Web Development'),
('Node.js', 'Web Development'),
('MySQL', 'Database'),
('PostgreSQL', 'Database'),
('Photoshop', 'Design'),
('Illustrator', 'Design'),
('UI/UX Design', 'Design'),
('Digital Marketing', 'Marketing'),
('Content Writing', 'Writing'),
('Excel', 'Office'),
('Data Analysis', 'Analytics'),
('Machine Learning', 'AI/ML'),
('Guitar', 'Music'),
('Piano', 'Music'),
('Spanish', 'Languages'),
('French', 'Languages');

-- Insert sample admin user (password: admin123)
INSERT INTO users (username, email, password_hash, full_name, is_admin) VALUES
('admin', 'admin@skillswap.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System Administrator', TRUE);

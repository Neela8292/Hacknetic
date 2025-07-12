-- Create sample users with skills
USE skill_swap_platform;

-- Insert sample users (passwords are all 'password123')
INSERT INTO users (username, email, password_hash, full_name, location, availability, is_public) VALUES
('john_dev', 'john@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'John Smith', 'New York, NY', 'Weekends, Evenings', TRUE),
('sarah_designer', 'sarah@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Sarah Johnson', 'Los Angeles, CA', 'Weekdays after 6PM', TRUE),
('mike_musician', 'mike@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mike Rodriguez', 'Chicago, IL', 'Weekends', TRUE),
('emma_writer', 'emma@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Emma Davis', 'Austin, TX', 'Flexible schedule', TRUE),
('alex_chef', 'alex@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Alex Chen', 'San Francisco, CA', 'Weekends, Mornings', TRUE),
('lisa_teacher', 'lisa@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Lisa Wilson', 'Seattle, WA', 'After school hours', TRUE),
('david_photographer', 'david@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'David Brown', 'Miami, FL', 'Weekends', TRUE),
('anna_marketer', 'anna@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Anna Taylor', 'Boston, MA', 'Evenings, Weekends', TRUE);

-- Add skills offered by sample users
-- John (Developer) - offers programming skills, wants design
INSERT INTO user_skills_offered (user_id, skill_id, proficiency_level, description) VALUES
(2, 1, 'Expert', '5+ years of JavaScript development, React specialist'),
(2, 3, 'Advanced', 'Full-stack PHP development with Laravel'),
(2, 6, 'Intermediate', 'Node.js backend development');

INSERT INTO user_skills_wanted (user_id, skill_id, desired_level, description) VALUES
(2, 9, 'Beginner', 'Want to learn Photoshop for web design'),
(2, 11, 'Intermediate', 'Looking to improve UI/UX design skills');

-- Sarah (Designer) - offers design skills, wants programming
INSERT INTO user_skills_offered (user_id, skill_id, proficiency_level, description) VALUES
(3, 9, 'Expert', 'Professional graphic designer with 7 years experience'),
(3, 10, 'Advanced', 'Logo design and brand identity specialist'),
(3, 11, 'Expert', 'UI/UX design for web and mobile applications');

INSERT INTO user_skills_wanted (user_id, skill_id, desired_level, description) VALUES
(3, 1, 'Beginner', 'Want to learn JavaScript to better collaborate with developers'),
(3, 4, 'Beginner', 'Basic HTML/CSS to understand web development');

-- Mike (Musician) - offers music skills, wants tech
INSERT INTO user_skills_offered (user_id, skill_id, proficiency_level, description) VALUES
(4, 17, 'Expert', 'Professional guitarist, 10+ years experience, multiple genres'),
(4, 18, 'Advanced', 'Classical and contemporary piano');

INSERT INTO user_skills_wanted (user_id, skill_id, desired_level, description) VALUES
(4, 12, 'Beginner', 'Want to learn digital marketing for my music'),
(4, 1, 'Beginner', 'Interested in learning web development');

-- Emma (Writer) - offers writing skills, wants languages
INSERT INTO user_skills_offered (user_id, skill_id, proficiency_level, description) VALUES
(5, 13, 'Expert', 'Professional content writer and copywriter'),
(5, 12, 'Advanced', 'Content marketing and SEO writing');

INSERT INTO user_skills_wanted (user_id, skill_id, desired_level, description) VALUES
(5, 19, 'Intermediate', 'Want to improve my Spanish for travel writing'),
(5, 9, 'Beginner', 'Basic Photoshop for blog graphics');

-- Alex (Chef) - offers cooking, wants business skills
INSERT INTO user_skills_offered (user_id, skill_id, proficiency_level, description) VALUES
(6, 14, 'Advanced', 'Excel for restaurant management and inventory');

INSERT INTO user_skills_wanted (user_id, skill_id, desired_level, description) VALUES
(6, 12, 'Intermediate', 'Digital marketing for my restaurant'),
(6, 13, 'Beginner', 'Content writing for social media');

-- Lisa (Teacher) - offers languages, wants tech
INSERT INTO user_skills_offered (user_id, skill_id, proficiency_level, description) VALUES
(7, 19, 'Expert', 'Native Spanish speaker, certified teacher'),
(7, 20, 'Advanced', 'French language instruction');

INSERT INTO user_skills_wanted (user_id, skill_id, desired_level, description) VALUES
(7, 14, 'Intermediate', 'Excel for grade management'),
(7, 4, 'Beginner', 'HTML/CSS for creating educational websites');

-- David (Photographer) - offers photography, wants editing
INSERT INTO user_skills_offered (user_id, skill_id, proficiency_level, description) VALUES
(8, 9, 'Advanced', 'Portrait and event photography');

INSERT INTO user_skills_wanted (user_id, skill_id, desired_level, description) VALUES
(8, 10, 'Intermediate', 'Advanced Illustrator techniques'),
(8, 16, 'Beginner', 'Machine learning for photo organization');

-- Anna (Marketer) - offers marketing, wants data skills
INSERT INTO user_skills_offered (user_id, skill_id, proficiency_level, description) VALUES
(9, 12, 'Expert', 'Digital marketing specialist with 6 years experience'),
(9, 13, 'Advanced', 'Content creation and copywriting');

INSERT INTO user_skills_wanted (user_id, skill_id, desired_level, description) VALUES
(9, 15, 'Intermediate', 'Data analysis for marketing insights'),
(9, 2, 'Beginner', 'Python for marketing automation');

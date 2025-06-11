CREATE TABLE users (
  id serial NOT NULL,
  name text NOT NULL,
  paternal_lastname text NOT NULL,
  maternal_lastname text,
  type text,
  email text NOT NULL,
  password text NOT NULL,
  PRIMARY KEY(id)
);

CREATE TABLE sessions (
  id serial NOT NULL,
  user_id int NOT NULL,
  token text NOT NULL,
  expired boolean NOT NULL DEFAULT FALSE,
  FOREIGN KEY(user_id) REFERENCES users(id) ON UPDATE CASCADE,
  PRIMARY KEY(id)
);

CREATE TABLE majors (
  id serial NOT NULL,
  name text NOT NULL,
  PRIMARY KEY(id)
);

CREATE TABLE shifts (
  id serial NOT NULL,
  name text NOT NULL,
  PRIMARY KEY(id)
);

CREATE TABLE students (
  id varchar(10) NOT NULL,
  user_id int NOT NULL,
  major_id int NOT NULL,
  FOREIGN KEY(user_id) REFERENCES users(id) ON UPDATE CASCADE,
  FOREIGN KEY(major_id) REFERENCES majors(id) ON UPDATE CASCADE,
  PRIMARY KEY(id)
);

CREATE TABLE teachers (
  id varchar(10) NOT NULL,
  user_id int NOT NULL,
  FOREIGN KEY(user_id) REFERENCES users(id) ON UPDATE CASCADE,
  PRIMARY KEY(id)
);

CREATE TABLE groups (
  id serial NOT NULL,
  name text NOT NULL,
  shift_id int NOT NULL,
  major_id int NOT NULL,
  FOREIGN KEY(shift_id) REFERENCES shifts(id) ON UPDATE CASCADE,
  FOREIGN KEY(major_id) REFERENCES majors(id) ON UPDATE CASCADE,
  PRIMARY KEY(id)
);

CREATE TABLE subjects (
  id serial NOT NULL,
  name text NOT NULL,
  major_id int NOT NULL,
  FOREIGN KEY(major_id) REFERENCES majors(id) ON UPDATE CASCADE,
  PRIMARY KEY(id)
);

CREATE TABLE enrolled (
  student_id varchar(10) NOT NULL,
  group_id int NOT NULL,
  subject_id int NOT NULL,
  FOREIGN KEY(student_id) REFERENCES students(id) ON UPDATE CASCADE,
  FOREIGN KEY(group_id) REFERENCES groups(id) ON UPDATE CASCADE,
  FOREIGN KEY(subject_id) REFERENCES subjects(id) ON UPDATE CASCADE,
  PRIMARY KEY(student_id, group_id, subject_id)
);

CREATE TABLE taught (
  teacher_id varchar(10) NOT NULL,
  group_id int NOT NULL,
  subject_id int NOT NULL,
  FOREIGN KEY(teacher_id) REFERENCES teachers(id) ON UPDATE CASCADE,
  FOREIGN KEY(group_id) REFERENCES groups(id) ON UPDATE CASCADE,
  FOREIGN KEY(subject_id) REFERENCES subjects(id) ON UPDATE CASCADE,
  PRIMARY KEY(teacher_id, group_id, subject_id)
);

CREATE TABLE projects (
  id serial NOT NULL,
  name text NOT NULL,
  description text NOT NULL,
  group_id int NOT NULL,
  subject_id int NOT NULL,
  FOREIGN KEY(group_id) REFERENCES groups(id) ON UPDATE CASCADE,
  FOREIGN KEY(subject_id) REFERENCES subjects(id) ON UPDATE CASCADE,
  PRIMARY KEY(id)
);

CREATE TABLE project_student (
  project_id integer NOT NULL,
  student_id varchar(10) NOT NULL,
  FOREIGN KEY(student_id) REFERENCES students(id) ON UPDATE CASCADE,
  FOREIGN KEY(project_id) REFERENCES projects(id) ON UPDATE CASCADE,
  PRIMARY KEY(student_id, project_id)
);

CREATE TABLE signs (
  id serial NOT NULL,
  sign text NOT NULL,
  printed boolean NOT NULL DEFAULT FALSE,
  project_id integer NOT NULL UNIQUE,
  FOREIGN KEY(project_id) REFERENCES projects(id) ON UPDATE CASCADE,
  PRIMARY KEY(id)
);
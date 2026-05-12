create table if not exists todos (
    id int auto_increment primary key,
    title varchar(255) not null,
    is_done tinyint(1) not null default 0,
    created_at timestamp default current_timestamp
);

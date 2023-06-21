create table close_quotation(
    date Datetime,
    stock_no varchar(6),
    stock_name varchar(10),
    tot_volume bigint,
    tot_num bigint,
    tot_money bigint,
    open_price float,
    max_price float,
    min_price float,
    close_price float,
    up_down char(1),
    up_diff float,
    primary key (date,stock_no)
);

create table market_statistics(
    date Datetime,
    tot_money bigint,
    tot_volume bigint,
    tot_num bigint,
    primary key (date)
);

create table T_price(
    date Datetime,
    close_point float,
    up_down char(1),
    up_point float,
    up_per float,
    primary key (date)
);

create table T_remuneration(
    date Datetime,
    close_point float,
    up_down char(1),
    up_point float,
    up_per float,
    primary key (date)
);

create table stock(
    stock_no varchar(6),
    stock_name varchar(10),
    primary key(stock_no)
) default charset=utf8mb4;

CREATE TABLE account(
    Account varchar(15),
    Password varchar(15),
    primary key(Account)
);

CREATE TABLE test(
    stock_no varchar(6),
    date datetime,
    open_price float,
    close_price float,
    max_price float,
    min_price float
);

create table favorite(
    Account varchar(15),
    stock_no varchar(6),
    primary key (Account,stock_no),
    foreign key (Account) references account(Account)
);

create table migrations
(
    id        serial
        primary key,
    migration varchar(255) not null,
    batch     integer      not null
);

alter table migrations
    owner to case_user;

create table personal_access_tokens
(
    id             bigserial
        primary key,
    tokenable_type varchar(255) not null,
    tokenable_id   bigint       not null,
    name           varchar(255) not null,
    token          varchar(64)  not null
        constraint personal_access_tokens_token_unique
            unique,
    abilities      text,
    last_used_at   timestamp(0),
    expires_at     timestamp(0),
    created_at     timestamp(0),
    updated_at     timestamp(0)
);

alter table personal_access_tokens
    owner to case_user;

create index personal_access_tokens_tokenable_type_tokenable_id_index
    on personal_access_tokens (tokenable_type, tokenable_id);

create table products
(
    id         bigserial
        primary key,
    name       varchar(255)   not null,
    stock_code varchar(255)   not null,
    price      numeric(10, 2) not null,
    created_at timestamp(0),
    updated_at timestamp(0),
    deleted_at timestamp(0)
);

alter table products
    owner to case_user;

create table customers
(
    id           bigserial
        primary key,
    name_surname varchar(255) not null,
    email        varchar(255) not null,
    username     varchar(255) not null,
    created_at   timestamp(0),
    updated_at   timestamp(0),
    deleted_at   timestamp(0)
);

alter table customers
    owner to case_user;

create table orders
(
    id             bigserial
        primary key,
    customer_id    bigint         not null
        constraint orders_customer_id_foreign
            references customers,
    total_amount   numeric(10, 2) not null,
    discount_total numeric(10, 2) not null,
    final_price    numeric(10, 2) not null,
    created_at     timestamp(0),
    updated_at     timestamp(0),
    deleted_at     timestamp(0)
);

alter table orders
    owner to case_user;

create table product_quantity
(
    id             bigserial
        primary key,
    product_id     bigint  not null
        constraint product_quantity_product_id_foreign
            references products,
    stock_quantity integer not null,
    created_at     timestamp(0),
    updated_at     timestamp(0),
    deleted_at     timestamp(0)
);

alter table product_quantity
    owner to case_user;

create index product_quantity_product_id_index
    on product_quantity (product_id);

create index product_quantity_stock_quantity_index
    on product_quantity (stock_quantity);

create table stock_movements
(
    id         bigserial
        primary key,
    product_id bigint       not null
        constraint stock_movements_product_id_foreign
            references products,
    type       varchar(255) not null,
    quantity   integer      not null,
    order_id   bigint       not null
        constraint stock_movements_order_id_foreign
            references orders,
    created_at timestamp(0),
    updated_at timestamp(0),
    deleted_at timestamp(0)
);

alter table stock_movements
    owner to case_user;

create table categories
(
    id          bigserial
        primary key,
    name        varchar(255) not null,
    slug        varchar(255),
    description varchar(255),
    created_at  timestamp(0),
    updated_at  timestamp(0),
    deleted_at  timestamp(0)
);

alter table categories
    owner to case_user;

create table discounts
(
    id                  bigserial
        primary key,
    name                varchar(255)  not null,
    type                varchar(255)  not null
        constraint discounts_type_check
            check ((type)::text = ANY
                   ((ARRAY ['percentage'::character varying, 'fixed'::character varying, 'free_item'::character varying])::text[])),
    value               numeric(5, 2) not null,
    category_id         bigint,
    min_quantity        integer,
    min_total           numeric(10, 2),
    discount_start_date timestamp(0),
    discount_end_date   timestamp(0),
    created_at          timestamp(0),
    updated_at          timestamp(0),
    deleted_at          timestamp(0)
);

alter table discounts
    owner to case_user;

create table product_categories
(
    id          bigserial
        primary key,
    product_id  bigint not null
        constraint product_categories_product_id_foreign
            references products,
    category_id bigint not null
        constraint product_categories_category_id_foreign
            references categories,
    created_at  timestamp(0),
    updated_at  timestamp(0),
    deleted_at  timestamp(0)
);

alter table product_categories
    owner to case_user;

create index product_categories_product_id_index
    on product_categories (product_id);

create index product_categories_category_id_index
    on product_categories (category_id);

create table category_relationships
(
    id                 bigserial
        primary key,
    parent_category_id bigint not null
        constraint category_relationships_parent_category_id_foreign
            references categories,
    child_category_id  bigint not null
        constraint category_relationships_child_category_id_foreign
            references categories,
    created_at         timestamp(0),
    updated_at         timestamp(0),
    deleted_at         timestamp(0)
);

alter table category_relationships
    owner to case_user;

create table order_products
(
    id         bigserial
        primary key,
    order_id   bigint  not null
        constraint order_products_order_id_foreign
            references orders,
    product_id bigint  not null
        constraint order_products_product_id_foreign
            references products,
    quantity   integer not null,
    created_at timestamp(0),
    updated_at timestamp(0),
    deleted_at timestamp(0)
);

alter table order_products
    owner to case_user;

create table order_discounts
(
    id          bigserial
        primary key,
    discount_id bigint not null
        constraint order_discounts_discount_id_foreign
            references discounts,
    order_id    bigint not null
        constraint order_discounts_order_id_foreign
            references orders,
    deleted_at  timestamp(0),
    created_at  timestamp(0),
    updated_at  timestamp(0)
);

alter table order_discounts
    owner to case_user;


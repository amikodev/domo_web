
create database `domo` character set utf8 collate utf8_general_ci;

set names 'utf8';

-- Устройство --
drop table if exists dom_Device;
create table dom_Device(
    id int not null auto_increment,
    `type` int not null,            -- тип устройства
    caption text,                   -- название
    pin int,                        -- номер pin'а
    datechange datetime,            -- дата изменения значения
    `value` text,                   -- актуальное значение
    parentID int,                   -- родительское устройство
    onewireID varchar(16),          -- OneWire ID
    connectType int not null,       -- тип подключения
    params text,                    -- параметры в формате json

    primary key (id)
) default character set utf8;


-- Прослушиватели --
drop table if exists dom_Listen;
create table dom_Listen(
    id int not null auto_increment,
    deviceID int not null,          -- ID устройства
    `type` int not null,            -- тип прослушивателя

    primary key (id)
) default character set utf8;

-- Сценарий -- 
drop table if exists dom_Scenario;
create table dom_Scenario(
    id int not null auto_increment,

    caption varchar(255),           -- название
    delay int default 0,            -- задержка перед выполнением
    deviceID int not null,          -- ID устройства
    -- scenarioComponent varchar(255), -- компонент сценария (наследники ScenarioComponent)
    content text,                   -- php скрипт
    actived boolean default true,   -- активен

    primary key (id)
) default character set utf8;

-- Параметры сценария -- 
drop table if exists dom_ScenarioParam;
create table dom_ScenarioParam(
    id int not null auto_increment,
    scenarioID int not null,        -- сценарий
    `name` varchar(255) not null,   -- имя параметра
    `value` text,                   -- значение параметра

    primary key (id)
) default character set utf8;

-- История значений устройства --
drop table if exists dom_DeviceHistory;
create table dom_DeviceHistory(
    id int not null auto_increment,
    deviceID int not null,
    datechange datetime,            -- дата изменения значения
    `value` text,                   -- значение

    primary key (id)
) default character set utf8;

-- Сцены --
drop table if exists dom_Scene;
create table dom_Scene(
    id int not null auto_increment,
    caption varchar(255),           -- название
    image varchar(255),             -- изображение
    primary key (id)
) default character set utf8;

-- Устройства сцены --
drop table if exists dom_SceneDevice;
create table dom_SceneDevice(
    id int not null auto_increment,
    sceneID int not null,           -- сцена
    deviceID int not null,          -- устройство
    x int,                          -- позиция устройства на сцене
    y int,
    angle int,                      -- угол поворота
    width int,                      -- ширина
    height int,                     -- высота
    primary key (id)
) default character set utf8;

-- Виджеты сцены --
drop table if exists dom_SceneWidget;
create table dom_SceneWidget(
    id int not null auto_increment,
    sceneID int not null,           -- сцена
    caption varchar(255) not null,  -- название
    `type` int not null,            -- тип виджета
    params text,                    -- параметры в формате json

    primary key (id)
) default character set utf8;

-- NFC авторизация --
drop table if exists dom_NFCAuth;
create table dom_NFCAuth(
    id int not null auto_increment,

    uuid varchar(14) not null,      -- ID пропуска
    block4 varchar(32) not null,    -- требуемые данные блока 4 для авторизации
    fio text,                       -- ФИО пользователя, к которому привязан пропуск

    scenarioID int,                 -- вызов сценария при успешной авторизации

    primary key (id)
) default character set utf8;

-- История NFC авторизаций --
drop table if exists dom_NFCAuthHistory;
create table dom_NFCAuthHistory(
    id int not null auto_increment,
    naID int,                       -- NFCAuth.id при успешной операции

    uuid varchar(14) not null,      -- ID пропуска
    block4 varchar(32) not null,    -- прочитанные данные блока 4 для авторизации

    dateoperation datetime not null,    -- дата операции
    `state` int,                    -- статус операции

    primary key (id)
) default character set utf8;

-- Плагины --
drop table if exists dom_Plugin;
create table dom_Plugin(
    id int not null auto_increment,
    `name` varchar(255) not null,   -- компонент application.components.plugins.*
    caption varchar(255),           -- название
    actived boolean default true,   -- активен
    params text,                    -- параметры в формате json
    
    primary key (id)
) default character set utf8;

-- Параметры системы --
drop table if exists dom_SystemParam;
create table dom_SystemParam(
    id int not null auto_increment,
    `name` varchar(255) not null,   -- имя
    caption varchar(255),           -- название
    `value` text,                   -- значение
    primary key (id),
    index n(`name`)
) default character set utf8;

insert into dom_SystemParam values(null, 'sms_valid_numbers', 'Разрешённые номера', '');
insert into dom_SystemParam values(null, 'email-log-route_emails', 'Log emails', 'webmaster@email.ru');



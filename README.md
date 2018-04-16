## 案例说明
* socket 服务器开启: `php start.php start`

* 客户端进入 TestClient 目录: 执行 `php -S localhost:8000` 即可访问

* MVC 主动向客户端发送 socket: `php SocketFromMVC.php`

* 范例内容:
    * socket 特殊接口: 客户端给自己打标签 (joinGroup)
    * socket 转发接口: 在业务规划好的情况下提供转发接口让 客户端 把数据原样转发给 另一个客户端.
    * MVC 框架做 客户端 示例


## GatewayWorker2.x 3.x 手册
* http://doc2.workerman.net/326102


## 基于Gateway、Worker进程模型
* GatewayWorker 使用经典的 Gateway 和 Worker 进程模型。

* **Gateway进程** 负责 _维持客户端连接_，并转发客户端的数据给 **Worker进程** 处理;

* **Worker进程** 负责 _处理实际的业务逻辑_，并将结果推送给对应的客户端;

* **Gateway服务** 和 **Worker服务** 可以分开部署在不同的服务器上，实现分布式集群。


## 工作原理
* http://doc2.workerman.net/326104


### 关于 Register 进程
* Gateway 进程相当于 Workerman 的 Master

* Worker 进程还是 Workerman 的 Worker

* Gateway、BusinessWorker进程 启动后向 **Register服务进程** 发起长连接注册自己

* **Register 服务** 收到 Gateway 的注册后，把所有 Gateway 的通讯地址保存在内存中

* **Register 服务** 收到 BusinessWorker 的注册后，把内存中所有的 Gateway 的通讯地址发给BusinessWorker


### BusinessWorker 进程
* BusinessWorker 的业务逻辑入口全部在 Events.php 中，包括:
    * `onWorkerStart`   进程启动事件 (进程事件)  -- 一般用不到;
    * `onConnect`       连接事件 (客户端事件)    -- 比较少用到;
    * `onMessage`       消息事件（客户端事件)    -- 必用;
    * `onClose`         连接关闭事件 (客户端事件) -- 比较常用到;
    * `onWorkerStop`    进程退出事件(进程事件)    -- 几乎用不到.


## 入门指南
* http://doc2.workerman.net/326105

* 多项目

* 连接数超过 1000 在线:
    * linux 内核优化: http://doc.workerman.net/315302
    * event 扩展安装: http://doc.workerman.net/315116

* Applications 下面的项目文件说明:
    * 一般来说开发者只需要关注 `Applications/YourApp/Events.php`
    * 其它 `start_gateway.php` `start_businessworker.php` `start_register.php` 分别是进程启动脚本，开发者一般不需要改动这三个文件。三个脚本统一由根目录的 `start.php` 启动

* start_gateway.php:
    * start_gateway.php 为 gateway 进程启动脚本，主要定义了 **客户端连接** 的端口号、协议等信息，具体参见 Gateway类的使用一节。

    * 客户端连接的就是 start_gateway.php 中初始化的 Gateway 端口。

* start_businessworker.php:
    * start_businessworker.php 为 businessWorker 进程启动脚本，也即是调用 Events.php的业务处理进程，具体参见 BusinessWorker类的使用一节。

start_register.php
start_register.php为注册服务启动脚本，用于协调GatewayWorker集群内部Gateway与Worker的通信，参见Register类使用一节。

注意：客户端不要连接Register服务端口，客户端应该连接Gateway端口


## 启动与停止
* http://doc2.workerman.net/326106

## 与 PHP 框架结合
* http://doc2.workerman.net/326107

* 当 PHP 框架需要向浏览器(客户端)主动推送数据时, 在 PHP框架中调用 GateWay的API: 使用 CatewayClient(https://github.com/walkor/GatewayClient) 完成单向推送

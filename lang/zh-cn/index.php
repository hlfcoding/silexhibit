<?php if (!defined('SITE')) exit('No direct script access allowed');

/**
* Translation
*
* Simplified Chinese
* 
* @version 1.0
* @author Lu Gao
*/

// GB2312

$words = array 
(
	// BASIC INTERFACE PARTS
	'indexhibit' => 'Indexhibit',
	'preferences' => '偏好设定',
	'help' => '帮助',
	'logout' => '退出',
	
	// BASIC MAIN NAV PARTS
	'content' => '内容',
	'admin' => '管理',
	'pages' => '页面',
	'settings' => '设置',
	'section' => '分类',
	'exhibits' => '展示内容',
	'stats' => '浏览统计',
	'settings' => '设置',
	'system' => '系统',
	'user' => '用户',
	
	// error messages
	'login err' => '密码或用户名错误，请重试',
	'router err 1' => '该页面不存在',
	'class not found' => '找不到分类',
	'database is unavailable' => '数据库不可用',
	'error finding settings' => '搜索设置错误',
	'no menu created' => '菜单未建立',
	'no results' => '错误,无效',
		
	// Location descriptors
	'main' => '主菜单',
	'edit' => '编辑',
	'preview' => '预览',
	'search' => '搜索',
	'new' => '新建',
	
	// some tabs
	'text' => '文字',
	'images' => '图片',
	
	// meed tp tranlsate the default sections
	'project' => '项目',
	'on-going' => '进行中',
	
	// generic forms parts & labels
	'page title' => '页面标题',
	'add page' => '增加新页面',
	'submit' => '提交',
	'update' => '更新',
	'required' => '必填',
	'optional' => '可选',
	'hidden' => '隐藏',
	'delete' => '删除',
	'publish' => '发布',
	'unpublish' => '不发布',
	'choose file' => '选择文件',
	
	'exhibition name' => '展示标题',
	'advanced mode' => '高级模式',
	'theme' => '主题',
	'api key' => 'API Key',
	'image max' => '图片最大尺寸',
	'thumb max' => '缩略图最大尺寸',
	'image quality' => '图像质量',
	'freelancer' => '自由状态',
	'pre nav text' => '导航条开头文字',
	'post nav text' => '导航条末尾文字',
	'html allowed' => '(允许使用HTML)',
	'update order' => '更新顺序',
	'view' => '查看',
	'no images' => '没有图片',
	'add images' => '增加图片',
	'image title' => '图片名称',
	'image caption' => '图片注释',
	'attach more files' => '添加更多文件',
	'page process' => '处理页面',
	'page hide' => '隐藏页面',
	'background image' => '背景图片',
	'background color' => '背景颜色',
	'edit color' => '点击色块编辑',
	'uploaded' => '上传完毕',
	'updated' => '更新完毕',
	'width' => '宽',
	'height' => '高',
	'kb' => 'KB',
	'saving' => '保存中...',
	
	// editor buttons & such
	'bold' => '粗体',
	'italic' => '斜体',
	'underline' => '底部划线',
	'links manager' => '链接管理',
	'files manager' => '文件管理',
	'save preview' => '保存/预览',
	'upload' => '上传',
	'make selection' => '选择',
	'on' => '开',
	'off' => '关',
	
	// popup editor parts
	"create link" => "建立链接",
	"hyperlink" => "超链接",
	"urlemail" => "网址/电子邮件",
	"none found" => "未找到",
	"allowed filetypes" => "允许的文件格式",
	"upload files" => "上传文件",
	"attach more" => "添加更多文件",
	"title" => "标题",
	"edit file info" => "修改文件信息",
	"description" => "文件描述",
	"if applicable" => "(如果可用)",
	
	// statistics related things
	'referrers' => '访问来源',
	'page visits' => '页面访问统计',
	
	'total' => '总访问',
	'unique' => '直接访问',
	'refers' => '来自链接',
	
	'since' => '统计起始日期',
	'ip' => 'IP',
	'country' => '国家',
	'date' => '日期',
	'keyword' => '关键字',
	'total pages' => '总页面数',
	'next' => '下一页',
	'previous' => '前一页',
	'visits' => '访问人数',
	'page' => '访问页面',
	
	'this week' => '本周',
	'today' => '今日',
	'yesterday' => '昨日',
	'this month' => '本月',
	'last week' => '上周',
	'year' => '年度',
	'last month' => '上个月',
	'top 10 referrers' => '前10名来源',
	'top 10 keywords' => '前10名关键字',
	'top 10 countries' => '前10名国家',
	'past 30' => '30天前',
	
	'2 weeks ago' => '2周前',
	'3 weeks ago' => '3周前',
	'4 weeks ago' => '4周前',
	'2 days ago' => '2天前',
	'3 days ago' => '3天前',
	'4 days ago' => '4天前',
	'5 days ago' => '5天前',
	'6 days ago' => '6天前',
	'2 months ago' => '2个月前',
	'3 months ago' => '3个月前',
	'4 months ago' => '4个月前',
	'5 months ago' => '5个月前',
	'6 months ago' => '6个月前',
	'7 months ago' => '7个月前',
	'8 months ago' => '8个月前',
	'9 months ago' => '9个月前',
	'10 months ago' => '10个月前',
	'11 months ago' => '11个月前',
	
	// system strings
	'name' => '名',
	'last name' => '姓',
	'email' => '电子邮件',
	'login' => '登录名',
	'password' => '密码',
	'confirm password' => '确认密码',
	'number chars' => '6-12个字母',
	'if change' => '如果更改',
	'time now' => '现在时间?',
	'time format' => '时间格式',
	'your language' => '语言',
	
	// installation
	'htaccess ok' => '.htaccess文件已准备完成...',
	'htaccess not ok' => "请在config.php文件内,将'MODREWRITE'设为'false'...",
	'files ok' => "/files 文件夹可写入...",
	'files not ok' => "/files 文件夹不可写入...",
	'filesgimgs ok' => "/files/gimgs 文件夹可写入...",
	'filesgimgs not ok' => "/files/gimgs 文件夹不可写入...",
	'try db setup now' => "现在建立数据库",
	'continue' => "继续",
	'please correct errors' => "请更正以上错误",
	'refresh page' => "然后刷新本页面",
	'goto forum' => "请前往<a href='http://www.indexhibit.org/forum/'>帮助论坛</a> 获取帮助.",
	'edit config' => "您需要合适的数据库设置来对config.php文件进行编辑",
	'refresh this page' => "完成此步骤后请刷新此页面.",
	'contact webhost' => "如果您有疑问，请联系您的主机托管服务商获取帮助。",
	'database is ready' => "数据库已建立完成",
	'tried installing' => "已尝试安装目录",
	'cannot install' => "无法连接或安装数据库",
	'check config' => "请再次检查您的config设置",
	'default login' => "初始登录名为 index1, 密码为 exhibit",
	'change settings' => "登录后可立即更改用户名, 密码以及网站设定",
	'lets login' => "来登录吧!",
	'freak out' => "出现了极其严重的错误- 崩溃!",
	
	// javascript confirm pops
	'are you sure' => '你确定吗?',
	
	
	// additions 17.03.2007
	'change password' => '更改密码',
	'project year' => '项目年份',
	'report' => '报告',
	'email address' => '电子邮件地址',
	'below required' => '以下需要作为Indexhibit报告之用',
	'from registration' => '来自Indexhibit注册',
	'register at' => '注册在',
	'background tiling' => '重复背景',
	'page process' => 'HTML编辑',
	'hide page' => '在首页中隐藏本页面',
	'allowed formats' => '只允许jpg, png和gif 格式文件',
	'filetypes' => '文件类型',
	'updating' => '更新中...',
	
	// additions 18.03.2007
	'max file size' => '最大文件容量',
	'exhibition format' => '展示模式',
	'view full size' => '全尺寸观看',
	'cancel' => '取消',
	'view site' => '查看你的网站',
	'store' => '储存',
	
	// additions 19.03.2007
	'config ok' => "/ndxz-studio/config 文件夹可写入...",
	'config not ok' => "/ndxz-studio/config 文件夹不可写入...",
	'database server' => "数据库服务器",
	'database username' => "数据库用户名",
	'database name' => "数据库名",
	'database password' => "数据库密码",
	
	// additions 10.04.2007
	'create new section' => "建立新分类",
	'section name' => "分类名称",
	'folder name' => "文件夹名称",
	'chronological' => "按年份排列",
	'sectional' => "按分类排列",
	'use editor' => "所见即所得编辑",
	'organize' => "整理",
	'sections' => "分类",
	'path' => "路径",
	'section order' => "分类顺序",
	'reporting' => "报告",
	'sure delete section' => "你确定吗? 这样也会删除此分类中的所有页面",
	'projects section' => "项目分类",
	'about this site' => "关于本站",
	'additional options' => "附加选项",
	'add section' => "增加分类",
	
	// additions 21.04.2007
	'section display' => "显示分类标题",
	
	// additions - no date yet
	'invalid input' => "无效输入",
	
	'the_end' => '结束'
	//'' => '',
);


?>
{include file='../_units/libraries.tpl'}
{include file='../_units/header.tpl'}

<div class="row p-3">
	<h4>Репозиторий</h4>
	<p><a href="https://github.com/vdetman/testnws">https://github.com/vdetman/testnws</a></p>
	
	<h4>Основной контроллер:</h4>
	<p><a href="https://github.com/vdetman/testnws/blob/master/app/controllers/NewsController.php">/app/controllers/NewsController.php</a></p>
	
	<h4>Сервис новостей:</h4>
	<p><a href="https://github.com/vdetman/testnws/blob/master/app/modules/News/News.php">/app/modules/News/News.php</a></p>
	
	<h4>AJAX и прочая асинхронность:</h4>
	<p><a href="https://github.com/vdetman/testnws/blob/master/public/js/news.js">/public/js/news.js</a></p>

	<h4>Обновление</h4>
	<p>Новости подтягиваются через RSS https://lenta.ru/rss. При этом, новые категории (рубрики) создаются автоматически</p>

	<h4>ВНИМАНИЕ!</h4>
	<p>При изменении данных напрямую в БД, нужно сбросить КЭШ</p>
	
	<h2>Модель данных</h2>
	<h4>Структура</h4>
	<code><pre>{$sqlStruct}</pre></code>
	
	<h2>Ключевые запросы SQL</h2>
	
	<h4>Просчет общего кол-ва новостей в рубриках</h4>
	<em>Используется уникальный индекс <b>UK_news_relations</b></em>
	<code><pre>SELECT rubric_id, COUNT(news_id) AS items_count FROM news_relations GROUP BY rubric_id;</pre></code>
	
	<h4>Получение списка рубрик с их "родителями" для построения дерева</h4>
	<em>Используется индекс <b>FK_news_rubrics_tree_rubric_id_c</b></em>
	<code><pre>SELECT 
  r.rubric_id         AS id, 
  r.rubric_name       AS name, 
  rt.parent_rubric_id AS parent_id 
FROM news_rubrics r 
INNER JOIN news_rubrics_tree rt ON rt.child_rubric_id = r.rubric_id 
ORDER BY r.rubric_sort, r.rubric_id;</pre></code>
	
	<h4>Получение списка новостей конкретной рубрики</h4>
	<em>Используется индекс <b>UK_news_relations</b></em>
	<code><pre>SELECT SQL_CALC_FOUND_ROWS * FROM news n 
INNER JOIN news_relations r ON r.news_id = n.news_id 
WHERE r.rubric_id IN (1) 
ORDER BY created_at DESC LIMIT 15 OFFSET 0;</pre></code>
</div>

{include file='../_units/footer.tpl'}
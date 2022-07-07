<?php
declare(strict_types=1);

namespace BaserCore\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * WidgetAreasFixture
 */
class WidgetAreasFixture extends TestFixture
{

    public $import = ['table' => 'widget_areas'];

    /**
     * Records
     *
     * @var array
     */
    public $records = [
        [
            'id' => '1',
            'name' => 'ウィジェットエリア',
            'widgets' => 'YToxOntpOjA7YToxOntzOjc6IldpZGdldDEiO2E6OTp7czoyOiJpZCI7czoxOiIxIjtzOjQ6InR5cGUiO3M6MTI6IuODhuOCreOCueODiCI7czo3OiJlbGVtZW50IjtzOjQ6InRleHQiO3M6NjoicGx1Z2luIjtzOjA6IiI7czo0OiJzb3J0IjtpOjE7czo0OiJuYW1lIjtzOjUyOiJXSURHRVQgQVJFQSA8c3Bhbj7jgqbjgqPjgrjjgqfjg4Pjg4jjgqjjg6rjgqI8L3NwYW4+IjtzOjQ6InRleHQiO3M6NTY3OiI8dWw+DQo8bGkgc3R5bGU9IndpZHRoOjUwJTtmbG9hdDpsZWZ0O3RleHQtYWxpZ246Y2VudGVyOyI+PHA+PGEgaHJlZj0iaHR0cDovL2Jhc2VyY21zLm5ldCIgdGFyZ2V0PSJfYmxhbmsiPjxpbWcgc3JjPSJodHRwOi8vYmFzZXJjbXMubmV0L2ltZy9ibnJfYmFzZXJjbXMuanBnIiBhbHQ9IuOCs+ODvOODneODrOODvOODiOOCteOCpOODiOOBq+OBoeOCh+OBhuOBqeOBhOOBhENNU+OAgWJhc2VyQ01TIj48L2E+PC9wPg0KPHA+PHNtYWxsPuOBk+OBrumDqOWIhuOBr+OAgeOCpuOCo+OCuOOCp+ODg+ODiOOCqOODquOCoueuoeeQhuOCiOOCiue3qOmbhuOBp+OBjeOBvuOBmeOAgjwvc21hbGw+PC9wPjwvbGk+DQo8bGkgc3R5bGU9IndpZHRoOjUwJTtmbG9hdDpsZWZ0O3RleHQtYWxpZ246Y2VudGVyOyI+PGEgaHJlZj0iaHR0cDovL2Jhc2VyY21zLm5ldC8iID48aW1nIHNyYz0iaHR0cDovL2Jhc2VyY21zLm5ldC9pbWcvd2hhdF9iYXNlcmNtcy5naWYiIGFsdD0i44Kz44O844Od44Os44O844OI44K144Kk44OI44Gr44Gh44KH44GG44Gp44GE44GEQ01T44CAQmFzZXJDTVMiPjwvYT48L2xpPg0KPC91bD4iO3M6OToidXNlX3RpdGxlIjtzOjE6IjEiO3M6Njoic3RhdHVzIjtzOjE6IjEiO319fQ==',
            'modified' => null,
            'created' => '2015-01-27 12:56:53'
        ],
        [
            'id' => '2',
            'name' => 'ブログサイドバー',
            'widgets' => 'YTo0OntpOjA7YToxOntzOjc6IldpZGdldDEiO2E6OTp7czoyOiJpZCI7czoxOiIxIjtzOjQ6InR5cGUiO3M6MjQ6IuODluODreOCsOOCq+ODrOODs+ODgOODvCI7czo3OiJlbGVtZW50IjtzOjEzOiJibG9nX2NhbGVuZGFyIjtzOjY6InBsdWdpbiI7czo0OiJCbG9nIjtzOjQ6InNvcnQiO2k6MTtzOjQ6Im5hbWUiO3M6MjQ6IuODluODreOCsOOCq+ODrOODs+ODgOODvCI7czoxNToiYmxvZ19jb250ZW50X2lkIjtzOjE6IjEiO3M6OToidXNlX3RpdGxlIjtzOjE6IjAiO3M6Njoic3RhdHVzIjtzOjE6IjEiO319aToxO2E6MTp7czo3OiJXaWRnZXQyIjthOjEwOntzOjI6ImlkIjtzOjE6IjIiO3M6NDoidHlwZSI7czozMDoi44OW44Ot44Kw44Kr44OG44K044Oq44O85LiA6KanIjtzOjc6ImVsZW1lbnQiO3M6MjI6ImJsb2dfY2F0ZWdvcnlfYXJjaGl2ZXMiO3M6NjoicGx1Z2luIjtzOjQ6IkJsb2ciO3M6NDoic29ydCI7aToyO3M6NDoibmFtZSI7czoyMToi44Kr44OG44K044Oq44O85LiA6KanIjtzOjU6ImNvdW50IjtzOjE6IjEiO3M6MTU6ImJsb2dfY29udGVudF9pZCI7czoxOiIxIjtzOjk6InVzZV90aXRsZSI7czoxOiIxIjtzOjY6InN0YXR1cyI7czoxOiIxIjt9fWk6MjthOjE6e3M6NzoiV2lkZ2V0MyI7YToxMTp7czoyOiJpZCI7czoxOiIzIjtzOjQ6InR5cGUiO3M6Mjc6IuaciOWIpeOCouODvOOCq+OCpOODluS4gOimpyI7czo3OiJlbGVtZW50IjtzOjIxOiJibG9nX21vbnRobHlfYXJjaGl2ZXMiO3M6NjoicGx1Z2luIjtzOjQ6IkJsb2ciO3M6NDoic29ydCI7aTozO3M6NDoibmFtZSI7czoyNzoi5pyI5Yil44Ki44O844Kr44Kk44OW5LiA6KanIjtzOjU6ImNvdW50IjtzOjI6IjEyIjtzOjEwOiJ2aWV3X2NvdW50IjtzOjE6IjEiO3M6MTU6ImJsb2dfY29udGVudF9pZCI7czoxOiIxIjtzOjk6InVzZV90aXRsZSI7czoxOiIxIjtzOjY6InN0YXR1cyI7czoxOiIxIjt9fWk6MzthOjE6e3M6NzoiV2lkZ2V0NCI7YToxMDp7czoyOiJpZCI7czoxOiI0IjtzOjQ6InR5cGUiO3M6MTU6IuacgOi/keOBruaKleeovyI7czo3OiJlbGVtZW50IjtzOjE5OiJibG9nX3JlY2VudF9lbnRyaWVzIjtzOjY6InBsdWdpbiI7czo0OiJCbG9nIjtzOjQ6InNvcnQiO2k6NDtzOjQ6Im5hbWUiO3M6MTU6IuacgOi/keOBruaKleeovyI7czo1OiJjb3VudCI7czoxOiI1IjtzOjE1OiJibG9nX2NvbnRlbnRfaWQiO3M6MToiMSI7czo5OiJ1c2VfdGl0bGUiO3M6MToiMSI7czo2OiJzdGF0dXMiO3M6MToiMSI7fX19',
            'modified' => null,
            'created' => '2015-01-27 12:56:53'
        ],
    ];

}

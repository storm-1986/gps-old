/*
 * Leaflet.draw assumes that you have already included the Leaflet library.
 */

L.drawVersion = '0.2.4-dev';

L.drawLocal = {
	draw: {
		toolbar: {
			actions: {
				title: 'Отмена',
				text: 'Отмена'
			},
			undo: {
				title: 'Удалить последнюю точку',
				text: 'Удалить последнюю точку'
			},
			buttons: {
				polyline: 'Измерить расстояние',
				polygon: 'Нарисовать многоугольник',
				rectangle: 'Нарисовать квадрат',
				circle: 'Нарисовать круг',
				marker: 'Поставить маркер'
			}
		},
		handlers: {
			circle: {
				tooltip: {
					start: 'Кликните и тяните курсор чтобы нарисовать круг.'
				},
				radius: 'Радиус'
			},
			marker: {
				tooltip: {
					start: 'Кликните чтобы поставить маркер.'
				}
			},
			polygon: {
				tooltip: {
					start: 'Кликните чтобы начать рисовать фигуру.',
					cont: 'Кликните чтобы продолжить рисовать фигуру.',
					end: 'Кликните в первую точку чтобы закончить рисовать фигуру.'
				}
			},
			polyline: {
				error: '<strong>Error:</strong> shape edges cannot cross!',
				tooltip: {
					start: 'Кликните по карте чтобы начать рисовать линию.',
					cont: 'Кликните в другой точке чтобы продолжить рисовать линию.',
					end: 'Закончите рисование линии двойным кликом.'
				}
			},
			rectangle: {
				tooltip: {
					start: 'Кликните и тяните курсор чтобы нарисовать квадрат.'
				}
			},
			simpleshape: {
				tooltip: {
					end: 'Release mouse to finish drawing.'
				}
			}
		}
	},
	edit: {
		toolbar: {
			actions: {
				save: {
					title: 'Сохранить изменения.',
					text: 'Сохранить'
				},
				cancel: {
					title: 'Отменить редактирование, убрать все изменения.',
					text: 'Отмена'
				}
			},
			buttons: {
				edit: 'Редактировать фигуры.',
				editDisabled: 'Нет фигур для редактирования.',
				remove: 'Удалить фигуры.',
				removeDisabled: 'Нет фигур для удаления.'
			}
		},
		handlers: {
			edit: {
				tooltip: {
					text: 'Переместите точку или маркер чтобы изменить фигуру.',
					subtext: 'Нажмите отмена для отката.'
				}
			},
			remove: {
				tooltip: {
					text: 'Нажмите на фигуру для удаления'
				}
			}
		}
	}
};

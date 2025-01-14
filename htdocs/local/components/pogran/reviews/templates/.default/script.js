function openEditForm(id, text, entityType, valid, entity) {
  BX.SidePanel.Instance.open("pogran:reviews", {
    width: 470,
    title: "Изменить отзыв",
    events: {
      onLoad: function () {
        editForm.querySelector('[name="TEXT"]').value = text;
        editForm.querySelector('[name="ENTITY_TYPE"]').value = entityType;
        editForm.querySelector('[name="VALID"]').checked =
          valid == 1 ? true : false;
        editForm.querySelector('[name="ENTITY"]').value = entity;
      },
      onClose: function () {
        this.destroy();
      },
    },
    contentCallback: function (slider) {
      return BX.Runtime.loadExtension("ui.sidepanel.layout").then(() => {
        return BX.UI.SidePanel.Layout.createContent({
          title: "Изменение отзыва",
          extensions: ["ui.buttons", "ui.forms"],
          design: { section: false, margin: true },
          buttons({ cancelButton, SaveButton }) {
            return [
              new SaveButton({
                onclick: () => {
                  let editForm = document.getElementById("editForm");
                  let reviewText =
                    editForm.querySelector('[name="TEXT"]').value;
                  let reviewEntityType = editForm.querySelector(
                    '[name="ENTITY_TYPE"]'
                  ).value;
                  let reviewValid =
                    editForm.querySelector('[name="VALID"]').checked;
                  let reviewEntity = 
                    editForm.querySelector('[name="ENTITY"]').value

                  BX.ajax
                    .runComponentAction("pogran:reviews", "editReview", {
                      mode: "class",
                      data: {
                        reviewId: id,
                        reviewText: reviewText,
                        reviewEntityType: reviewEntityType,
                        reviewValid: reviewValid,
                        reviewEntity: reviewEntity
                      },
                    })
                    .then(function (response) {
                      console.log(response);
                      if (response.status === 'success') {
                        BX.SidePanel.Instance.close();
                        let grid = BX.Main.gridManager.getInstanceById("reviews_list");
                        if(grid) {
                          grid.reloadTable();
                        }
                      } else {
                      }
                    });
                },
              }),
              cancelButton,
            ];
          },
          content: function () {
            return    `<div>
                          <h3>Изменение отзыва</h3>
						              <form method="POST" id="editForm">
                            <div class=ui-ctl ui-ctl-textarea">Отзыв: 
                                <textarea name="TEXT" class="ui-ctl-element ui-ctl-resize-y">${text}</textarea>
                            </div>
                            Тип связной сущности:
                            <div class="ui-ctl ui-ctl-after-icon ui-ctl-dropdown"> 
                                <div class="ui-ctl-after ui-ctl-icon-angle"></div>
                                <select name="ENTITY_TYPE" class="ui-ctl-element">
                                    <option value="PRODUCT">Товар</option>
                                    <option value="STORE">Магазин</option>
                                    <option value="MANAGER">Менеджер</option>
                                </select>
                            </div>
                            ID связной сущности:
                            <div class="ui-ctl ui-ctl-textbox">
	                            <input type="text" class="ui-ctl-element" name="ENTITY">
                            </div>
                            <label class="ui-ctl ui-ctl-checkbox">
                              <input type="checkbox" class="ui-ctl-element" name="VALID">
                              <div class="ui-ctl-label-text">Модерация</div>
                            </label>
						              </form>
                        </div>`;
          },
        });
      });
    },
  });
}

function deleteReview(id) {
  console.log(id);
  BX.ajax
  .runComponentAction("pogran:reviews", "deleteReview", {
    mode: "class",
    data: {
      reviewId: id,
    },
  })
  .then(function (response) {
    console.log(response);
    if (response.status === 'success') {
      alert('Отзыв удален');
      let grid = BX.Main.gridManager.getInstanceById("reviews_list");
      if(grid) {
        grid.reloadTable();
      }
    } else {

    }
  });
}

function openCreateForm() {
  BX.SidePanel.Instance.open("pogran:reviews", {
    width: 370,
    title: "Создать отзыв",
    contentCallback: function (slider) {
      return BX.Runtime.loadExtension("ui.sidepanel.layout").then(() => {
        return BX.UI.SidePanel.Layout.createContent({
          title: "Создание отзыва",
          extensions: ["ui.buttons", "ui.forms"],
          design: { section: false, margin: true },
          buttons({ cancelButton, SaveButton }) {
            return [
              new SaveButton({
                onclick: () => {
                  let editForm = document.getElementById("editForm");
                  let reviewText =
                    editForm.querySelector('[name="TEXT"]').value;
                  let reviewEntityType = editForm.querySelector(
                    '[name="ENTITY_TYPE"]'
                  ).value;
                  let reviewValid =
                    editForm.querySelector('[name="VALID"]').checked;
                  let reviewEntity = 
                    editForm.querySelector('[name="ENTITY"]').value
                  let reviewRating = 
                    editForm.querySelector('[name="RATING"]').value

                  BX.ajax
                    .runComponentAction("pogran:reviews", "createReview", {
                      mode: "class",
                      data: {
                        reviewText: reviewText,
                        reviewRating: reviewRating,
                        reviewEntityType: reviewEntityType,
                        reviewValid: reviewValid,
                        reviewEntity: reviewEntity,
                        // 'parentComponent': parentComponent,
                      },
                    })
                    .then(function (response) {
                      console.log(response);
                      if (response.status === 'success') {
                        alert('Отзыв создан');
                        BX.SidePanel.Instance.close();
                        let grid = BX.Main.gridManager.getInstanceById("reviews_list");
                        if(grid) {
                          grid.reloadTable();
                        }
                      } else {
                      }
                    });
                  // console.log(grid);

                  // alert('saved!');
                  // console.log(id);
                  //   BX.SidePanel.Instance.destroy(slider)
                  // BX.SidePanel.Instance.close();
                  // BX.SidePanel.Slider.destroy();
                  //   BX.SidePanel.Instance.destroy(slider);
                  // console.log(gridId);
                  // console.log(grid);
                  // if (grid) {
                  //   grid.reloadTable();
                  // }
                },
              }),
              cancelButton,
            ];
          },
          content: function () {
            return    `<div>
                          <h3>Создание отзыва</h3>
						              <form method="POST" id="editForm">
                            <div class=ui-ctl ui-ctl-textarea">Отзыв: 
                                <textarea name="TEXT" class="ui-ctl-element ui-ctl-resize-y"></textarea>
                            </div>
                            Рейтинг:
                            <div class="ui-ctl ui-ctl-textbox">
	                            <input type="text" class="ui-ctl-element" name="RATING">
                            </div>
                            Тип связной сущности:
                            <div class="ui-ctl ui-ctl-after-icon ui-ctl-dropdown"> 
                                <div class="ui-ctl-after ui-ctl-icon-angle"></div>
                                <select name="ENTITY_TYPE" class="ui-ctl-element">
                                    <option value="PRODUCT">Товар</option>
                                    <option value="STORE">Магазин</option>
                                    <option value="MANAGER">Менеджер</option>
                                </select>
                            </div>
                            ID связной сущности:
                            <div class="ui-ctl ui-ctl-textbox">
	                            <input type="text" class="ui-ctl-element" name="ENTITY">
                            </div>
                            <label class="ui-ctl ui-ctl-checkbox">
                              <input type="checkbox" class="ui-ctl-element" name="VALID">
                              <div class="ui-ctl-label-text">Модерация</div>
                            </label>
						              </form>
                        </div>`;
          },
        });
      });
    },
  });
}
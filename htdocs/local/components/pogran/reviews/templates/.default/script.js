function openEditForm(id, rating, text, entityType, valid, entity) {
  BX.SidePanel.Instance.open("pogran:reviews", {
    width: 470,
    title: "Изменить отзыв",
    events: {
      onLoad: function () {
        editForm.querySelector('[name="ID"]').value = id;
        editForm.querySelector('[name="TEXT"]').value = text;
        editForm.querySelector('[name="RATING"]').value = rating;
        editForm.querySelector('[name="ENTITY_TYPE"]').value = entityType;
        editForm.querySelector('[name="VALID"]').checked =
          valid == 'Y' ? true : false;
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
                  let errorFields = document.querySelectorAll(".error-field");
                  errorFields.forEach(function (error) {
                    error.remove();
                  });
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
                  let formData = new FormData(editForm);
                  BX.ajax
                    .runComponentAction("pogran:reviews", "editReview", {
                      mode: "class",
                      data: formData
                    })
                    .then(function (response) {
                      if (response.status === 'success') {
                        BX.SidePanel.Instance.close();
                        let grid = BX.Main.gridManager.getInstanceById("reviews_list");
                        if(grid) {
                          grid.reloadTable();
                        }
                      } else {
                      }
                    }, function(response) {
                      errors = response.errors;
                      errors.forEach(function (error) {
                        fieldname = error.customData.field;
                        errorMess = error.message;
                        const newElement = document.createElement("p");
                        newElement.innerHTML = errorMess;
                        newElement.classList.add("error-field");
                        const formField = document.querySelector(
                          "[name=" + fieldname + "]"
                        );
                        formField.insertAdjacentElement(
                          "afterend",
                          newElement
                        );
                      });
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
                            <input type="text" hidden name="ID">
                            Отзыв:
                            <div class=ui-ctl ui-ctl-textarea"> 
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

function deleteReview(id) {
  BX.ajax
  .runComponentAction("pogran:reviews", "deleteReview", {
    mode: "class",
    data: {
      reviewId: id,
    },
  })
  .then(function (response) {
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
    events: {
      onClose: function () {
        this.destroy();
      },
    },
    contentCallback: function (slider) {
      return BX.Runtime.loadExtension("ui.sidepanel.layout").then(() => {
        return BX.UI.SidePanel.Layout.createContent({
          title: "Создание отзыва",
          extensions: ["ui.buttons", "ui.forms"],
          design: { section: false, margin: true },
          buttons({ cancelButton, SaveButton }) {
            return [
              new SaveButton({
                type: 'submit',
                onclick: () => {
                  let errorFields = document.querySelectorAll(".error-field");
                  errorFields.forEach(function (error) {
                    error.remove();
                  });
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
                  let formData = new FormData(editForm);
                  BX.ajax
                    .runComponentAction("pogran:reviews", "createReview", {
                      mode: "class",
                      data: formData
                    })
                    .then(function (response) {
                      if (response.status === 'success') {
                        alert('Отзыв создан');
                        BX.SidePanel.Instance.close();
                        let grid = BX.Main.gridManager.getInstanceById("reviews_list");
                        if(grid) {
                          grid.reloadTable();
                        }
                      } else {
                      }
                    }, function(response) {
                      errors = response.errors;
                      errors.forEach(function (error) {
                        fieldname = error.customData.field;
                        errorMess = error.message;
                        const newElement = document.createElement("p");
                        newElement.innerHTML = errorMess;
                        newElement.classList.add("error-field");
                        const formField = document.querySelector(
                          "[name=" + fieldname + "]"
                        );
                        formField.insertAdjacentElement(
                          "afterend",
                          newElement
                        );
                      });
                  });
                },
              }),
              cancelButton,
            ];
          },
          content: function () {
            return    `<div>
                          <h3>Создание отзыва</h3>
						              <form method="POST" id="editForm">
                            Отзыв:
                            <div class=ui-ctl ui-ctl-textarea">
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

// any CSS you import will output into a single css file (app.css in this case)
import '../styles/front.scss';
import '../styles/back.scss';
//create a const getNiceMessage with "import"
import getNiceMessage from './get_nice_message';
import Shuffle from "shufflejs";




$('#rateSubmitBtn').on('click',function(e){
    e.preventDefault();
    //Vérification
    let val = $("#applicant_rating_note").val();
    if( val === ""){
        Swal.fire({
            icon: 'error',
            title: 'Pas si vite',
            text: 'Merci de répondre à la question avant de valider',
        })
    }
    else{
        Swal.fire({
            title: 'Valider votre commentaire ?',
            showDenyButton: true,
            allowOutsideClick: false,
            confirmButtonText: 'oui',
            denyButtonText: `Annuler`,
        }).then((result) => {
            /* Read more about isConfirmed, isDenied below */
            if (result.isConfirmed) {
                $("._form").submit();
            } else if (result.isDenied) {
                Swal.fire('Annulé !', '', 'info')
            }
        })
    }

})

const sizer1 = document.getElementById('dashboard2');
if(sizer1) {


    class Demo {
        constructor(element) {
            this.element = element;
            this.shuffle = new Shuffle(element, {
                itemSelector: '.picture-item',
                sizer: element.querySelector('.my-sizer-element'),
            });

            // Log events.
            this.addShuffleEventListeners();
            this._activeFilters = [];
            this.addFilterButtons();
            this.addSorting();
            this.addSearchFilter();
        }

        /**
         * Shuffle uses the CustomEvent constructor to dispatch events. You can listen
         * for them like you normally would (with jQuery for example).
         */
        addShuffleEventListeners() {
            this.shuffle.on(Shuffle.EventType.LAYOUT, (data) => {

            });
            this.shuffle.on(Shuffle.EventType.REMOVED, (data) => {

            });
        }

        addFilterButtons() {
            const options = document.querySelector('.filter-options');
            if (!options) {
                return;
            }

            const filterButtons = Array.from(options.children);
            const onClick = this._handleFilterClick.bind(this);
            filterButtons.forEach((button) => {
                button.addEventListener('click', onClick, false);
            });
        }

        _handleFilterClick(evt) {
            const btn = evt.currentTarget;
            const isActive = btn.classList.contains('active');
            const btnGroup = btn.getAttribute('data-group');

            this._removeActiveClassFromChildren(btn.parentNode);

            let filterGroup;
            if (isActive) {
                btn.classList.remove('active');
                filterGroup = Shuffle.ALL_ITEMS;
            } else {
                btn.classList.add('active');
                filterGroup = btnGroup;
            }

            this.shuffle.filter(filterGroup);
        }

        _removeActiveClassFromChildren(parent) {
            const {children} = parent;
            for (let i = children.length - 1; i >= 0; i--) {
                children[i].classList.remove('active');
            }
        }

        addSorting() {
            const buttonGroup = document.querySelector('.sort-options');
            if (!buttonGroup) {
                return;
            }
            buttonGroup.addEventListener('change', this._handleSortChange.bind(this));
        }

        _handleSortChange(evt) {
            // Add and remove `active` class from buttons.
            const buttons = Array.from(evt.currentTarget.children);
            buttons.forEach((button) => {
                if (button.querySelector('input').value === evt.target.value) {
                    button.classList.add('active');
                } else {
                    button.classList.remove('active');
                }
            });

            // Create the sort options to give to Shuffle.
            const {value} = evt.target;
            let options = {};

            function sortByDate(element) {
                return element.getAttribute('data-created');
            }

            function sortByTitle(element) {
                return element.getAttribute('data-title').toLowerCase();
            }

            if (value === 'date-created') {
                options = {
                    reverse: true,
                    by: sortByDate,
                };
            } else if (value === 'title') {
                options = {
                    by: sortByTitle,
                };
            }
            this.shuffle.sort(options);
        }

        // Advanced filtering
        addSearchFilter() {
            const searchInput = document.querySelector('.js-shuffle-search');
            if (!searchInput) {
                return;
            }
            searchInput.addEventListener('keyup', this._handleSearchKeyup.bind(this));
        }

        /**
         * Filter the shuffle instance by items with a title that matches the search input.
         * @param {Event} evt Event object.
         */
        _handleSearchKeyup(evt) {
            const searchText = evt.target.value.toLowerCase();
            this.shuffle.filter((element, shuffle) => {
                // If there is a current filter applied, ignore elements that don't match it.
                if (shuffle.group !== Shuffle.ALL_ITEMS) {
                    // Get the item's groups.
                    const groups = JSON.parse(element.getAttribute('data-groups'));
                    const isElementInCurrentGroup = groups.indexOf(shuffle.group) !== -1;
                    // Only search elements in the current group
                    if (!isElementInCurrentGroup) {
                        return false;
                    }
                }
                const titleElement = element.querySelector('.picture-item__title');
                const titleText = titleElement.textContent.toLowerCase().trim();
                return titleText.indexOf(searchText) !== -1;
            });
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        window.demo = new Demo(document.getElementById('grid'));
    });

}
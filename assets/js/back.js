
// any CSS you import will output into a single css file (app.css in this case)
//COLORS

import '../styles/back.scss';
import '../styles/_back_navbar.scss';

import {DataTable} from "simple-datatables";
import Shuffle, { ShuffleOptions, SortOptions } from 'shufflejs';

//import Swal from 'sweetalert2';
import { parseJSON } from 'jquery';


$(document).ready(function(){
    "use strict";

	var fullHeight = function() {

		$('.js-fullheight').css('height', $(window).height());
		$(window).resize(function(){
			$('.js-fullheight').css('height', $(window).height());
		});

	};
	fullHeight();

	$('#sidebarCollapse').on('click', function () {
         $('#sidebar').toggleClass('active');
    });

	//FORMULAIRE D'UPLOAD DE FICHIER
	let uploadFileInput = document.querySelector('.uploadFileInput');
	
	

	if(uploadFileInput != undefined){

		uploadFileInput.nextElementSibling.innerHTML = "Cliquer ici pour charger un ficher";

		uploadFileInput.addEventListener('change', function (e) {
			//Récupération du nom du fichier uploadé
			let file = document.getElementById("upload_file_uploadName").files[0];
	
			if(file){
				//Affichage du nom dans le label de l'input après upload 
				let nextSibling = e.target.nextElementSibling;
				nextSibling.innerText = file.name
			}else{
				let nextSibling = e.target.nextElementSibling;
				nextSibling.innerText = "Cliquer ici pour charger un ficher"
			}
			
	
		})
		$('.fileSubmitButton').on('click',function(e){
			e.preventDefault();
			//Vérification
			//1. fichier existant
			let file = document.getElementById("upload_file_uploadName").files[0];
			if(!file){
				
				Swal.fire({
					icon: 'error',
					title: 'Oops...',
					text: 'Veuillez charger un fichier !',
				  })
			}
			else{
				//2. taille de fichier correcte
				if(file.size <= 5120000){
					//3. type de fichier correct
					if (file.type == "application/pdf" || file.type == "application/vnd.openxmlformats-officedocument.presentationml.presentation" || file.type == 'application/vnd.ms-powerpoint' || file.type == 'image/png' || file.type == 'image/jpeg' || file.type == 'application/msword'|| file.type == 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'|| file.type == 'application/vnd.ms-excel'|| file.type == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') {
						
						Swal.fire({
							position: 'top-end',
							icon: 'success',
							title: 'Fichier chargé',
							showConfirmButton: true,
							allowOutsideClick: false,
							timer: 30000
						  }).then((result) => {
							
							if (result.isConfirmed) {
								
								$(".upload_form").submit()
							}
						  })
						
					}
					else{
						Swal.fire({
							icon: 'error',
							title: 'Oops...',
							text: 'Fichier non pris en charge !',
						  })
					}
				}
				else{
					Swal.fire({
						icon: 'error',
						title: 'Oops...',
						text: 'Taille de fichier supérieure à 5 Mo',
					  })
				}
				
			}
			
		})
	}
	//FORMULAIRE D'UPLOAD DE PHOTO
	let uploadPhotoInput = document.querySelector('.uploadPhotoInput');
	
	

	if(uploadPhotoInput != undefined){

		uploadPhotoInput.nextElementSibling.innerHTML = "Charger une photo du matériel";

		uploadPhotoInput.addEventListener('change', function (e) {
			//Récupération du nom du fichier uploadé
			let file = document.getElementById("upload_photo_uploadPhotoName").files[0];
	
			if(file){
				//Affichage du nom dans le label de l'input après upload 
				let nextSibling = e.target.nextElementSibling;
				nextSibling.innerText = file.name
			}else{
				let nextSibling = e.target.nextElementSibling;
				nextSibling.innerText = "Charger une photo du matériel"
			}
			
	
		})
		$('.fileSubmitButton2').on('click',function(e){
			e.preventDefault();
			//Vérification
			//1. fichier existant
			let file = document.getElementById("upload_photo_uploadPhotoName").files[0];
			if(!file){
				
				Swal.fire({
					icon: 'error',
					title: 'Oops...',
					text: 'Veuillez charger une photo !',
				  })
			}
			else{
				//2. taille de fichier correcte
				if(file.size <= 1048576){
					//3. type de fichier correct
					if ( file.type == 'image/jpeg') {
						
						Swal.fire({
							position: 'top-end',
							icon: 'success',
							title: 'Photo chargée',
							showConfirmButton: true,
							allowOutsideClick: false,
							timer: 30000
						  }).then((result) => {
							
							if (result.isConfirmed) {
								
								$(".upload_photo_form").submit()
							}
						  })
						
					}
					else{
						Swal.fire({
							icon: 'error',
							title: 'Oops...',
							text: 'Fichier non pris en charge !',
						  })
					}
				}
				else{
					Swal.fire({
						icon: 'error',
						title: 'Oops...',
						text: 'Taille de fichier supérieure à 1 Mo',
					  })
				}
				
			}
			
		})
	}

	$('.submitButton').on('click',function(e){
		e.preventDefault();
		//Vérification
	
		Swal.fire({
			title: 'Valider ?',
			showDenyButton: true,
			allowOutsideClick: false,
			confirmButtonText: 'Sauvegarder',
			denyButtonText: `Annuler`,
		  }).then((result) => {
			/* Read more about isConfirmed, isDenied below */
			if (result.isConfirmed) {
			  $("._form").submit();
			} else if (result.isDenied) {
			  Swal.fire('Annulé !', '', 'info')
			}
		  })
	  })

});
const dataTableFull = document.querySelector("#dataTableFull");
if(dataTableFull){
	const dataTa = new DataTable("#dataTableFull",{
		labels: {
			placeholder: "Rechercher...",
			perPage: "{select} / page",
			noRows: "Aucune donnée trouvée",
			info: " ",
		}
	});
}
const dataTableFull2 = document.querySelector("#dataTableFull2");
if(dataTableFull2){
	const dataTa = new DataTable("#dataTableFull2",{
		labels: {
			placeholder: "Rechercher...",
			perPage: "{select} / page",
			noRows: "Aucune donnée trouvée",
			info: " ",
		}
	});
}
const myTable = document.querySelector("#dataTableEquipment");
if(myTable){
	const dataTable = new DataTable("#dataTableEquipment",{
		searchable:false,
		labels: {
			perPage: "",
			noRows: "Aucune donnée trouvée",
			info: " ",
		}
	});
}
const myTable2 = document.querySelector("#dataTableTask");
if(myTable2){
	const dataTable = new DataTable("#dataTableTask",{
		searchable:false,
		labels: {
			perPage: "",
			noRows: "Aucune donnée trouvée",
			info: " ",
		}
	});
}

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


//***************************************************************************
// AJAX CALLER
//***************************************************************************

/* Fonction générique AJAX --> requête SecurityController.php
 *  Parameter 1 : action (String) appelle une fonction (requis)
 *  Parameter 2 : param (objet json) paramètres de la fonction (optionnel)
 *  Warning : Depuis JQuery 1.8 .success et .error functions sont dépréciés dans cette forme
 *  Ce caller utilise .done et .fail */
//VOIR https://github.com/M-LR/campuslab-old/blob/testing/ressources/js/chat.js
//ET https://github.com/M-LR/campuslab-old/blob/testing/app/ajaxcontroller.ctl.php
//voir SecurityController.php @Route("/ajaxCtl", name="ajaxCtl", methods={"GET"})

let notifer = {
		init: function (){
			 //Récupération des données en bases
			 const allLog =  ajaxCaller2.launch('log_event');

			 const result = JSON.parse(allLog);

			 $("#ajaxModal .modal-body ul").empty();
			 let count = 0;
			 $.each(result, (index,value)=>{

					if (value !== false){
						
						if(value.logEventName !== undefined){
							$("#ajaxModal .modal-body ul").append("<li>"+ value.type +" : <a href='"+ value.url +"'>"+ value.logEventName +"</a> le "+ value.logEventCreatedAt +"</li>");
						}
						

					}
					if (value === true){
						let audio = new Audio("/build/audio/notification.32bd321f.mp3");
						audio.play();

						$("#ajaxModalBtn").html("<i class=\"far fa-bell\"></i><span class=\"badge bg-danger text-white rounded-pill\"> new</span>");
					}
					count++;



			 })
		},
}


let ajaxCaller2 = {
	launch: function(value, param) {
		
		let response = "";
		param = param || {};

		$.ajax({
			dataType: "json",
			async: false,
			type: 'GET',
			data: {action: value, parameter: param},
			url: '/ajaxCtl'
		}).done(function (data) {
			response = data;
		}).fail(function (data, textStatus, jqXHR) {
			response = "error";
		});
		
		return response;
	}
}

setInterval(notifer.init,20000);
notifer.init();


$("#ajaxModalBtn").on('click',()=>{
	$("#ajaxModalBtn").html('<i class="far fa-bell"></i>');
})



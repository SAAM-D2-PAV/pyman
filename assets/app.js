/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */
import $ from 'jquery';
global.$ = $;
//Twitter Bootstrap
import 'bootstrap';


// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.scss';
//animate css
import "animate.css";

// assets/app.js
// (or assets/bootstrap.js - and then import it from app.js)

import { startStimulusApp } from '@symfony/stimulus-bridge';

export const app = startStimulusApp(require.context(
    '@symfony/stimulus-bridge/lazy-controller-loader!./controllers',
    true,
    /\.(j|t)sx?$/
));



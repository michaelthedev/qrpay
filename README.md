
<div id="top"></div>

<!-- PROJECT SHIELDS -->
<!--
*** I'm using markdown "reference style" links for readability.
*** Reference links are enclosed in brackets [ ] instead of parentheses ( ).
*** See the bottom of this document for the declaration of the reference variables
*** for contributors-url, forks-url, etc. This is an optional, concise syntax you may use.
*** https://www.markdownguide.org/basic-syntax/#reference-style-links
-->
[![Contributors][contributors-shield]][contributors-url]
[![Forks][forks-shield]][forks-url]
[![Stargazers][stars-shield]][stars-url]
[![Issues][issues-shield]][issues-url]
[![MIT License][license-shield]][license-url]
[![LinkedIn][linkedin-shield]][linkedin-url]



<!-- PROJECT LOGO -->
<br />
<div align="center">
  <a href="https://github.com/michael-arawole/qrpay">
    <img src="https://demo.logad.net/qrpay/assets/img/qrpay-logo.png" alt="Logo" width="auto" height="200">
  </a>

  <h3 align="center">QrPay</h3>

  <p align="center">
    <a href="https://demo.logad.net/qrpay">View Demo</a>
    ·
    <a href="https://github.com/michael-arawole/qrpay/issues">Report Bug</a>
    ·
    <a href="https://github.com/michael-arawole/qrpay/issues">Request Feature</a>
  </p>
</div>


<!-- ABOUT THE PROJECT -->
## About The Project

[![QrPay Screen Shot][product-screenshot]](https://demo.logad.net/qrpay)

Very simple
* You register, the system generates a unique ID
* You login with your unique ID
* Your details are fetched from the API and updated at an interval
* Use your userID to receive money or use your QR code
* Send money by entering the receipients user id or scanning their code

The projects includes the Postman API collection too

* [Postman Documentation](https://documenter.getpostman.com/view/10657913/UVyswb74)
* [API collection](https://www.getpostman.com/collections/ba63b1a1b9c0126fa56d)
<p align="right">(<a href="#top">back to top</a>)</p>



### Built With

This section should list any major frameworks/libraries used to bootstrap your project. Leave any add-ons/plugins for the acknowledgements section. Here are a few examples.

* [PHP](https://php.net/)
* [Bootstrap](https://getbootstrap.com)
* [JQuery](https://jquery.com)

<p align="right">(<a href="#top">back to top</a>)</p>


### Installation

_How to install._

1. Navigate to api/core/config.php
2. Edit the credentials
   ```php
   $siteurl = "<URL-THE-PROJECT-IS-LOCATED>";
   ```
   
   ```php
   $dbuser = "<DATABASE-USERNAME>";
   ```
   
3. Upload qrpay.sql to your database
4. Save and open the url in your browser

<p align="right">(<a href="#top">back to top</a>)</p>

## Next Update
1. Adding personal payment link
2. Developer API system (later update)

<!-- CONTRIBUTING -->
## Contributing

Contributions are what make the open source community such an amazing place to learn, inspire, and create. Any contributions you make are **greatly appreciated**.

If you have a suggestion that would make this better, please fork the repo and create a pull request. You can also simply open an issue with the tag "enhancement".
Don't forget to give the project a star! Thanks again!

1. Fork the Project
2. Create your Feature Branch
3. Commit your Changes
4. Push to the Branch
5. Open a Pull Request

<p align="right">(<a href="#top">back to top</a>)</p>



<!-- LICENSE -->
## License

Distributed under the MIT License.

<p align="right">(<a href="#top">back to top</a>)</p>



<!-- CONTACT -->
## Contact
Michael Arawole - [@MichaelArawole](https://twitter.com/michaelarawole) - mycodemichael@gmail.com

<p align="right">(<a href="#top">back to top</a>)</p>



<!-- ACKNOWLEDGMENTS -->
## Acknowledgments

Special thanks to these guys for their amazing tool

* [Icons8](https://icons8.com)
* [Notyf](https://github.com/caroso1222/notyf)
* [Qrcode.js](https://github.com/davidshimjs/qrcodejs)
* [Qr Scanner](https://github.com/nimiq/qr-scanner)
<p align="right">(<a href="#top">back to top</a>)</p>

<!-- MARKDOWN LINKS & IMAGES -->
<!-- https://www.markdownguide.org/basic-syntax/#reference-style-links -->
[contributors-shield]: https://img.shields.io/github/contributors/michael-arawole/qrpay.svg?style=for-the-badge
[contributors-url]: https://github.com/michael-arawole/qrpay/graphs/contributors
[forks-shield]: https://img.shields.io/github/forks/michael-arawole/qrpay.svg?style=for-the-badge
[forks-url]: https://github.com/michael-arawole/qrpay/network/members
[stars-shield]: https://img.shields.io/github/stars/michael-arawole/qrpay.svg?style=for-the-badge
[stars-url]: https://github.com/michael-arawole/qrpay/stargazers
[issues-shield]: https://img.shields.io/github/issues/michael-arawole/qrpay.svg?style=for-the-badge
[issues-url]: https://github.com/michael-arawole/qrpay/issues
[license-shield]: https://img.shields.io/github/license/michael-arawole/qrpay.svg?style=for-the-badge
[license-url]: https://github.com/michael-arawole/qrpay/blob/master/LICENSE.txt
[linkedin-shield]: https://img.shields.io/badge/-LinkedIn-black.svg?style=for-the-badge&logo=linkedin&colorB=555
[linkedin-url]: https://linkedin.com/in/michaelarawole/
[product-screenshot]: screenshot.jpg

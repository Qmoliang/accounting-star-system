# accounting-star-system
This project is based on MySQL+bootstrap+php.

The original source code is from https://github.com/siamon123/warehouse-inventory-system. I modify it and use it as my subject's project. The usage of this website is just the same as his introduction. Thanks for Siamon123's help.

We generate some types of data crawling from TaoBao website. Also, we generate some fake sale data for testing the effect of website. We find some errors and fix some of them. Illustrate it here.

## Shortcoming.

1. `Paging`. If you add too many goods or medias. The list will out of range and can't be showed correctly. So We add Paging technology in some important php file. In actual, there are still some web pages acquired to be paged, such as user,user group and media. If you want to complete it, just imitate product.php and add some paging code in each file. Some comments point out that they can be encapsulated in a class. Try it if you're interested.

2. `Show`. The original exhibition for products' information mix all kinds of information together ordered by its id. We add the function so that user can choose the kind of product for showing. Because of limited time, we didn't add other functions further. There are so many items which user can input by themselves. We also find that in add_product.php, selecting media is also difficult since with the increasing of picture, the options will be so large that it can't be showed. Even if it can be showed, choosing the corresponding one for a product is also a difficult thing. My suggestion is that changing it into a input box with a choose button which user can open the file that contains media. Also, the media can only be uploaded one by one, I tried to modify it in order to bulk load medium but failed. Hope it can be solved later.

3. `NULL`. If the informations needed doesn't exist, it'll causes error and can't return the true result. So we test some conditions and avoid them. If user wants to find products that belong to a certain category and no one belongs to it, it will jump to add_product.php other than false. Other errors are similar to this. 

4. `Time`. The time in the web page is standard time. I added the number directly so that it becomes Beijing Time. It's better to set it in local time with specified codes. I set the timer in some web pages so it can return the time which the query costs in MySQL. It's for checking the rationality of database design. I found that the user group and user are actually the same things, the status 'active' or 'inactive' doesn't come into effect. What's more, I build it in December, 2017. When it's in 2018. The original data can't be showed in performance. I guess the inner time is updated and the products and sales added previously are cancelled. So even if they are still in database. we can't see it correctly. In details, it still need to be checked. So the lifespan of website is only one year. It cause some queries meaningless. 

I've showed all the modifications, problems and suggestions, hoping that someone can fix it in the future. I'll update this project if I make further process.

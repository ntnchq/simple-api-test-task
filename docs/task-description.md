**API Service Implementation Required:**

1. Description:

   The service is an article management system. Each article can have one or more tags attached to it, and for each such connection, the attachment date must be saved. The data structure includes the following entities:
* Article: contains a unique identifier (ID) and title.
* Tag: characterized by a unique identifier (ID) and name.


2. API Capabilities:

    1. Tag Management:
* Create/edit a tag. The request returns information about the tag.


2. Article Management:
* Create/edit an article with the ability to attach tags. The request should return information about the article.
* Article deletion - complete, without the possibility of restoration.


3. Getting Information About Articles:
* Getting a complete list of articles with the ability to filter by tags. The filter can contain multiple tags, and only those articles that have all the specified tags should be displayed. All tags attached to the articles are also displayed.
* Getting information about an article by its ID along with all attached tags.


3. Implementation Requirements:

* Development should be carried out using Symfony and PHP 8.3, MySQL 8.x best practices.
* Input and output data should be in JSON format.
* No authorization mechanism implementation is required.

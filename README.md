# Dynamic form for expo's

There is currently no storage back-end. All dynamic variables are extracted from the url path, then rewritten in .htaccess if it's not an existing file. 

It is a simplified version of the forms developed for ebs-v2 theme, with all server-side validation removed since it will only run on devices under our control. 

## URL path components:
```

http://ebs-platform.com/[source]/[medium]/[pageTitle]/[isoMarket]/[language]/[fromDate: dd-mmm-yyyy]/[toDate: dd-mmm-yyyy]

```

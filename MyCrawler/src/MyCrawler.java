
import edu.uci.ics.crawler4j.crawler.Page;
import edu.uci.ics.crawler4j.crawler.WebCrawler;
import edu.uci.ics.crawler4j.parser.HtmlParseData;
import edu.uci.ics.crawler4j.url.WebURL;

import java.io.File;

import java.util.Set;
import java.util.regex.Pattern;

public class MyCrawler extends WebCrawler {
    static Writer fetch, visit, urls;
    static Integer counter;
    
    Pattern domainFilter = Pattern.compile(".*www\\.chicagotribune\\.com.*");

    private static final Pattern requirePatterns = Pattern.compile(
            ".*(\\.(html|doc|pdf|bmp|gif|jpe?g|png|tiff?))$");
    
    
    static CrawlStat crawlstat;
    
    
    synchronized static Integer getCounter(){
        if (counter == null){
            counter = new Integer(0);
        }
        return counter;
    }
    
    synchronized static Writer getFetch(){
        if (fetch == null){
            fetch = new Writer("./data/fetch_Chicago_Tribune.csv");
        }
        return fetch;
    }

    synchronized static Writer getVisit(){
        if (visit == null){
            visit = new Writer("./data/visit_Chicago_Tribune.csv");
        }
        return visit;
    }

    synchronized static Writer getUrls(){
        if (urls == null){
            urls = new Writer("./data/urls_Chicago_Tribune.csv");
        }
        return urls;
    }
    
    synchronized static CrawlStat getCrawlStat(){
        if (crawlstat == null){
        		crawlstat = new CrawlStat();
        }
        return crawlstat;
    }
    

    public void onStart(){
        fetch = MyCrawler.getFetch();
        visit = MyCrawler.getVisit();
        urls = MyCrawler.getUrls();
        crawlstat = MyCrawler.getCrawlStat();
        counter = MyCrawler.getCounter();
    }

    @Override
    public void onBeforeExit(){
    		crawlstat.show();
        fetch.close();
        visit.close();
        urls.close();
    }

    @Override
    public boolean shouldVisit(Page referringPage, WebURL url) {
        String href = url.getURL().toLowerCase();
        crawlstat.incProcessedPages();        
        if(domainFilter.matcher(href).matches()) {
        		if(requirePatterns.matcher(href).matches())
        			return true;
        		else {
        			String contentType = referringPage.getContentType();
                if (contentType.contains("text/html")
                		|| contentType.contains("image")
                    || contentType.contains("application/pdf")){
                		return true;
                }
                return false;
        		}
        }
        return false;
    }

    @Override
    protected void handlePageStatusCode(WebURL webUrl, int statusCode, String statusDescription) {
    		counter++;
		System.out.println(counter);
        String url = webUrl.getURL().toLowerCase();
        String line = "\"" + url + "\"," + statusCode + "\n";
        fetch.write(line);
        crawlstat.incCode(statusCode);
    }

    @Override
    public void visit(Page page) {
    		crawlstat.incProcessedPages();
        if (page.getParseData() instanceof HtmlParseData) {
        		String url = page.getWebURL().getURL().toLowerCase();
            String contentType = page.getContentType();
            if(contentType.contains(";"))
            		contentType = contentType.split(";")[0];
            /*if (contentType.contains("text/html")
            		|| contentType.contains("image")
            		|| contentType.contains("application/pdf")
            		|| requirePatterns.matcher(url).matches()){*/
            		
            		HtmlParseData parseData = (HtmlParseData) page.getParseData();
            		Set<WebURL> links = parseData.getOutgoingUrls();
            		crawlstat.incTotalLinks(links.size());
            		String size = Integer.toString(page.getContentData().length);
            		//write visit
            		String visitline = "\"" + url + "\",\"" + size + " bytes\"," + links.size() + ",\"" +  contentType + "\"\n";
                visit.write(visitline);
                
                //write url
                for (WebURL link : links) {
                		String nurl = link.getURL();
                		crawlstat.uniqueURL.add(nurl);
                		String indicator;
                    if(domainFilter.matcher(nurl).matches()) {
                    		indicator = "OK";
                    		crawlstat.uniqueInURL.add(nurl);
                    }else {
                    		indicator = "N_OK";
                    		crawlstat.uniqueOutURL.add(nurl);
                    }
                    String urlline = "\"" + nurl + "\"," + indicator + "\n";
                    urls.write(urlline);
                }
            //}
        }
    }

}
import edu.uci.ics.crawler4j.crawler.CrawlConfig;
import edu.uci.ics.crawler4j.crawler.CrawlController;
import edu.uci.ics.crawler4j.fetcher.PageFetcher;
import edu.uci.ics.crawler4j.robotstxt.RobotstxtConfig;
import edu.uci.ics.crawler4j.robotstxt.RobotstxtServer;

public class Controller {
    public static void main(String[] args) throws Exception {
        String crawlStorageFolder = "data/crawl";
        int numberOfCrawlers = 10;

        CrawlConfig config = new CrawlConfig();
        config.setCrawlStorageFolder(crawlStorageFolder);
        config.setUserAgentString("Sophie121");
        config.setPolitenessDelay(200);
        config.setMaxDepthOfCrawling(16);
        config.setMaxPagesToFetch(20000);

        config.setIncludeBinaryContentInCrawling(true);
        config.setResumableCrawling(false);

        PageFetcher pageFetcher = new PageFetcher(config);
        RobotstxtConfig robotstxtConfig = new RobotstxtConfig();
        robotstxtConfig.setEnabled(false);
        RobotstxtServer robotstxtServer = new RobotstxtServer(robotstxtConfig, pageFetcher);
        CrawlController controller = new CrawlController(config, pageFetcher, robotstxtServer);

        String domain = "https://www.newsday.com/";
        String domain1 ="http://www.chicagotribune.com/";
        String domain2 ="https://www.chicagotribune.com/";
        String domain3 ="http://chicagotribune.com/";
        String domain4 ="https://chicagotribune.com/";
        controller.addSeed(domain1);
        //controller.addSeed(domain2);
        controller.addSeed(domain3);
        //controller.addSeed(domain4);
        controller.start(MyCrawler.class, numberOfCrawlers);
    }
}
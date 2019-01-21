import java.util.HashSet;
import java.util.Set;

public class CrawlStat {
    private int totalProcessedPages;
    private long totalLinks;
    private long totalTextSize;
    private int count200;
    private int count300;
    private int count400;
    
    public Set<String> uniqueURL;
    public Set<String> uniqueInURL;
    public Set<String> uniqueOutURL;
    public CrawlStat() {
    		uniqueURL = new HashSet<String>();
    		uniqueInURL = new HashSet<String>();
    		uniqueOutURL = new HashSet<String>();
    }

    public int getTotalProcessedPages() {
        return totalProcessedPages;
    }

    public void setTotalProcessedPages(int totalProcessedPages) {
        this.totalProcessedPages = totalProcessedPages;
    }

    public void incProcessedPages() {
        this.totalProcessedPages++;
    }

    public long getTotalLinks() {
        return totalLinks;
    }

    public void setTotalLinks(long totalLinks) {
        this.totalLinks = totalLinks;
    }

    public long getTotalTextSize() {
        return totalTextSize;
    }

    public void setTotalTextSize(long totalTextSize) {
        this.totalTextSize = totalTextSize;
    }

    public void incTotalLinks(int count) {
        this.totalLinks += count;
    }

    public void incTotalTextSize(int count) {
        this.totalTextSize += count;
    }
    
    public void incCode(int code) {
    		if(code == 200)
    			count200++;
    		else if(code >= 300 && code < 400)
    			count300++;
    		else if(code >= 400)
    			count400++;
    }
    
    public void show() {
    		System.out.println("total Page: " + this.totalProcessedPages);
    		System.out.println("total link: " + this.totalLinks);
    		System.out.println("unique url: " + this.uniqueURL.size());
    		System.out.println("unique in url: " + this.uniqueInURL.size());
    		System.out.println("unique out url: " + this.uniqueOutURL.size());
    		System.out.println("count200: " + this.count200);
    		System.out.println("count300: " + this.count300);
    		System.out.println("count400: " + this.count400);
    }
}
package test1;

import java.io.DataInputStream;
import java.io.DataOutputStream;
import java.io.IOException;
import java.io.UnsupportedEncodingException;
import java.net.MalformedURLException;
import java.net.URL;
import java.net.URLEncoder;

import javax.net.ssl.HttpsURLConnection;

public class SslPost 
{

	public static void post() throws IOException
	{
		String httpsURL = "https://godaddy.com/";

		String query = "email="+URLEncoder.encode("abc@xyz.com","UTF-8"); 
		query += "&";
		query += "password="+URLEncoder.encode("abcd","UTF-8") ;

		URL myurl = new URL(httpsURL);
		HttpsURLConnection con = (HttpsURLConnection)myurl.openConnection();
		con.setRequestMethod("POST");

		con.setRequestProperty("Content-length", String.valueOf(query.length())); 
		con.setRequestProperty("Content-Type","application/x-www-form-urlencoded"); 
		con.setRequestProperty("User-Agent", "Mozilla/4.0 (compatible; MSIE 5.0;Windows98;DigExt)"); 
		con.setDoOutput(true); 
		con.setDoInput(true); 

		DataOutputStream output = new DataOutputStream(con.getOutputStream());  


		output.writeBytes(query);

		output.close();

		DataInputStream input = new DataInputStream( con.getInputStream() ); 



		for( int c = input.read(); c != -1; c = input.read() ) 
		System.out.print( (char)c ); 
		input.close(); 

		System.out.println("Resp Code:"+con .getResponseCode()); 
		System.out.println("Resp Message:"+ con .getResponseMessage()); 
	}
}

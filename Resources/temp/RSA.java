package test1;

import java.io.DataInputStream;
import java.io.InputStream;
import java.security.KeyFactory;
import java.security.PublicKey;
import java.security.spec.X509EncodedKeySpec;

import javax.crypto.Cipher;

import org.apache.commons.codec.binary.Base64;

public class RSA 
{
	private String PublicKeyEncrypt(byte[] data)
	{
	    PublicKey pk = null;
	    try
	    {
	        InputStream f = getAssets().open("publickey.der");
	        DataInputStream dis = new DataInputStream(f);
	        byte[] keyBytes;
	        keyBytes = new byte[(int)f.available()];
	        dis.readFully(keyBytes);
	        dis.close();
	    
	        X509EncodedKeySpec spec = new X509EncodedKeySpec(keyBytes);
	        KeyFactory kf = KeyFactory.getInstance("RSA");
	        pk = kf.generatePublic(spec);
	    } 
	    catch (Exception e) {
	        e.printStackTrace();
	    } 
	    
	    final byte[] cipherText = encrypt(data, pk);
	    return Base64.encodeToString(cipherText,Base64.DEFAULT);
	}
	    
	private static byte[] encrypt(byte[] data, PublicKey key) 
	{
	    byte[] cipherText = null;
	    try {
	      final Cipher cipher = Cipher.getInstance("RSA/ECB/PKCS1Padding");
	      cipher.init(Cipher.ENCRYPT_MODE, key);
	      cipherText = cipher.doFinal(data);
	    } 
	    catch (Exception e) {
	      e.printStackTrace();
	    }
	    return cipherText;
	}
}

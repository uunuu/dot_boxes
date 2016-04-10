package test1;

import java.io.UnsupportedEncodingException;
import java.math.BigInteger;
import java.security.NoSuchAlgorithmException;
import java.security.SecureRandom;
import java.security.MessageDigest;
import javax.crypto.spec.SecretKeySpec;
import javax.crypto.spec.IvParameterSpec;
import javax.crypto.Cipher;


/*
 * ################## EXAMPLE ####################################
String msg = "Hello" ;
AES s = new AES("d") ;

try {
System.out.println("==Java==");
System.out.println("plain: " + msg);
 
byte[] cipher = s.encrypt(msg);
 
System.out.print("cipher: ");
for (int i=0; i<cipher.length; i++)
System.out.print(new Integer(cipher[i])+" ");
System.out.println("");
 
String decrypted = s.decrypt(cipher);
 
System.out.println("decrypt: " + decrypted);
 
} catch (Exception e) {
e.printStackTrace();
}
}
*/


 
public class AES 
{		
	
	private static final int IVlength = 16 ;

	private SecureRandom random ;
	
	private byte [] key ;// 16 bytes
	private byte [] IV  ;// 16 bytes

	public AES(String Key) throws UnsupportedEncodingException,NoSuchAlgorithmException
	{
		random = new SecureRandom();
		
			byte[] bytesOfMessage = Key.getBytes("UTF-8");
			MessageDigest md = MessageDigest.getInstance("MD5");
			key = md.digest(bytesOfMessage);
			IV = generateRandomString(IVlength).getBytes("UTF-8") ;


	}
	
	
	public AES(byte [] sharedSecret) throws Exception
	{
		if(sharedSecret.length < 32)
		{
			throw new Exception("Shared Secret legnth is less than 32") ;
		}
		
		byte [] vector = new byte[IVlength] ;
		byte [] k      = new byte[16] ;
		for(int i=0;i<32;i++)
		{
			vector[i] = sharedSecret[i] ;
			k[i]      = sharedSecret[i+16] ;
		}
	}

	public byte[] encrypt(String plainText) throws Exception
	{
		Cipher cipher = Cipher.getInstance("AES/CBC/NoPadding", "SunJCE");
		SecretKeySpec secKey = new SecretKeySpec(key, "AES");
		cipher.init(Cipher.ENCRYPT_MODE, secKey,new IvParameterSpec(IV));
		byte [] multipleOf16 = paddToMultipleOf16WithSpaces(plainText.getBytes("UTF-8")) ;
		return cipher.doFinal(multipleOf16);
	}
	
	public String decrypt(byte[] cipherText) throws Exception
	{
		Cipher cipher = Cipher.getInstance("AES/CBC/NoPadding", "SunJCE");
		SecretKeySpec secKey = new SecretKeySpec(key, "AES");
		cipher.init(Cipher.DECRYPT_MODE, secKey,new IvParameterSpec(IV));
		return new String(cipher.doFinal(cipherText),"UTF-8");
	}


	private String generateRandomString(int stringLength)
	{
	    return new BigInteger(stringLength*6, random).toString(32).substring(0, stringLength);
	}
	
	private byte [] paddToMultipleOf16WithSpaces(byte [] array) throws UnsupportedEncodingException
	{
		int numberOfPaddingBytes = 16 - array.length%16 ;
		
		if(numberOfPaddingBytes != 0)
		{
			
			byte [] newArray = new byte[array.length+numberOfPaddingBytes] ;
			for(int i=0;i<array.length;i++)
			{
				newArray[i] = array[i] ;
			}
			
			for(int j=array.length;j<newArray.length;j++)
			{
				newArray[j] = ".".getBytes("UTF-8")[0] ;
			}
			
			array = newArray ;
		}
		return array ;
	}
	
	
	public byte [] getKey()
	{
		if(key == null)
		{
			return null ;
		}
		
		byte [] tmpKey = new byte[key.length] ;
		for(int i=0;i<tmpKey.length;i++)
		{
			tmpKey[i] = key[i] ;
		}
		
		return tmpKey ;
	}
	

	public byte [] getIV()
	{
		if(IV == null)
		{
			return null ;
		}
		
		byte [] tmpIV = new byte[IV.length] ;
		for(int i=0;i<tmpIV.length;i++)
		{
			tmpIV[i] = IV[i] ;
		}
		
		return tmpIV ;
	}
	
	public void setIV(byte [] newIV) throws Exception
	{
		if(newIV.length < AES.IVlength)
		{
			throw new Exception("IV legnth is less than " + AES.IVlength) ;
		}
		else
		{
			for(int i=0;i<AES.IVlength;i++)
			{
				IV[i] = newIV[i] ;
			}
		}
	}


}

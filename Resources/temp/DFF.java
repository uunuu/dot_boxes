package test1;

import java.math.BigInteger;
import java.security.AlgorithmParameterGenerator;
import java.security.AlgorithmParameters;
import java.security.KeyFactory;
import java.security.KeyPair;
import java.security.KeyPairGenerator;
import java.security.NoSuchAlgorithmException;
import java.security.PrivateKey;
import java.security.PublicKey;
import java.security.spec.InvalidParameterSpecException;
import java.security.spec.X509EncodedKeySpec;

import javax.crypto.KeyAgreement;
import javax.crypto.SecretKey;
import javax.crypto.spec.DHParameterSpec;
import javax.crypto.spec.SecretKeySpec;

/*
 * ############### EXAMPLE #################
DFF x = new DFF(keys) ;
DFF y = new DFF(keys) ;

String keys = DFF.genDhParams() ;

byte [] s = x.getSharedKey(y.publicKeyBytes) ;
byte [] s2= y.getSharedKey(x.publicKeyBytes) ;

*/

public class DFF 
{
    private PrivateKey privateKey ;
    public PublicKey publicKey ;
    public byte[] publicKeyBytes  ;
    
    byte[] otherPartyPublicKeyg ;
	
    
	
	

	// Returns a comma-separated string of 3 values.
	// The first number is the prime modulus P.
	// The second number is the base generator G.
	// The third number is bit size of the random exponent L.
    
	public static String genDhParams()
	{
	    try {
	        // Create the parameter generator for a 1024-bit DH key pair
	        AlgorithmParameterGenerator paramGen = AlgorithmParameterGenerator.getInstance("DH");
	        paramGen.init(1024);

	        // Generate the parameters
	        AlgorithmParameters params = paramGen.generateParameters();
	    	
	        DHParameterSpec dhSpec = (DHParameterSpec)params.getParameterSpec(DHParameterSpec.class);

	        // Return the three values in a string
	        return ""+dhSpec.getP()+","+dhSpec.getG()+","+dhSpec.getL();
	    } 
	    catch (InvalidParameterSpecException e) 
	    {
	    }
	   catch (NoSuchAlgorithmException e) 
	    {
	    } 
	    
	    return null;
	}
	
	
	
	public DFF(String DhParams)
	{
		parseKey(DhParams) ;
	}
	
	private void parseKey(String DhParams)
	{
		String[] values = DhParams.split(",");
		BigInteger p = new BigInteger(values[0]);
		BigInteger g = new BigInteger(values[1]);
		int l = Integer.parseInt(values[2]);

		try {
		    // Use the values to generate a key pair
		    KeyPairGenerator keyGen = KeyPairGenerator.getInstance("DH");
		    DHParameterSpec dhSpec = new DHParameterSpec(p, g, l);
		    keyGen.initialize(dhSpec);
		    KeyPair keypair = keyGen.generateKeyPair();

		    // Get the generated public and private keys
		    privateKey = keypair.getPrivate();
		    publicKey = keypair.getPublic();

		    // Send the public key bytes to the other party...
		    publicKeyBytes = publicKey.getEncoded();
		}
		catch(Exception e)
		{
			Log.print(e.getMessage());
		}
	}
	
	
	
	public byte[] getSharedKey(byte [] otherPartyPublicKey)
	{
	    PublicKey tmpPublicKey ;
		try
		{
		    // Convert the public key bytes into a PublicKey object
		    X509EncodedKeySpec x509KeySpec = new X509EncodedKeySpec(otherPartyPublicKey);
		    KeyFactory keyFact = KeyFactory.getInstance("DH");
		    tmpPublicKey = keyFact.generatePublic(x509KeySpec);
		    // Prepare to generate the secret key with the private key and public key of the other party
		    KeyAgreement ka = KeyAgreement.getInstance("DH");
		    ka.init(privateKey);
		    ka.doPhase(tmpPublicKey, true);
	
	        byte[] sharedSecret = ka.generateSecret();
	        
	       //Log.print(sharedSecret.toString());
	       // SecretKeySpec sk = new SecretKeySpec(sharedSecret, 0, 32 /* key length */, "AES");
		    // Specify the type of key to generate;
		    // see Listing All Available Symmetric Key Generators
		   // String algorithm = "AES";
	
		    // Generate the secret key
		   // SecretKey secretKey = ka.generateSecret(algorithm);
		    return sharedSecret ;
		}
		catch(Exception e)
		{
			Log.print(e.getMessage());
		}
		return null ;
	}
	
}

import java.lang.ref.SoftReference;
import java.util.LinkedList;



public class RequestObject<Listener>  {

	private SoftReference<Listener> doneListener;
	private SoftReference<OnRequestFailListener> failListener;
	private Class<?> listenerType;
	private Object sendObj;
	
	public static final class KeyValuePair {
		public String key, value;
		public KeyValuePair(String k, Object v) {
			key = k;
			value = v.toString();
		}
	}
	
	private LinkedList<KeyValuePair> sendParam;
	
	public void setListenerInterfaceClass(Class<?> clazz) {
		listenerType = clazz;
	}
	
	public void addParam(String key, Object value) {
		if(sendParam == null)
			sendParam = new LinkedList<KeyValuePair>();
		sendParam.add(new KeyValuePair(key, value));
	}
	
	public void setObject(Object obj) {
		sendObj = obj;
	}
	
	public RequestObject<Listener> onSuccess(Listener listener) {
		if(listener == null)
			doneListener = null;
		else
			doneListener = new SoftReference<Listener>(listener);
		return this;
	}
	
	public RequestObject<Listener> onFail(OnRequestFailListener listener) {
		if(listener == null)
			failListener = null;
		else
			failListener = new SoftReference<OnRequestFailListener>(listener);
		return this;
	}
}

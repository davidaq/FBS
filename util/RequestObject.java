package util;

import java.lang.ref.SoftReference;
import java.lang.ref.WeakReference;
import java.lang.reflect.Method;
import java.util.LinkedList;
import java.util.List;

import org.json.JSONObject;

import com.google.gson.Gson;

public class RequestObject<Listener> {

	public static String baseUrl;
	public static OnRequestFailListener globalOnFailListener;

	private WeakReference<Listener> doneListener;
	private SoftReference<OnRequestFailListener> failListener;
	private SoftReference<OnRequestProgressChangeListener> progressListener;
	@SuppressWarnings("unused")
	private Object doneListenerHolder, failListenerHolder, progressListenerHolder;
	private Class<?> listenerType;
	private Object sendObj;
	private String url;
	private long timestamp = System.nanoTime();
	
	public long getRequestTime() {
		return timestamp;
	}

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

	public void setUrl(String url) {
		this.url = url;
	}

	public String getUrl() {
		return url;
	}

	public void addParam(String key, Object value) {
		if (sendParam == null)
			sendParam = new LinkedList<KeyValuePair>();
		sendParam.add(new KeyValuePair(key, value));
	}

	public List<KeyValuePair> getParams() {
		return sendParam;
	}

	public void setObj(Object obj) {
		sendObj = obj;
	}

	public Object getObj() {
		return sendObj;
	}

	public RequestObject<Listener> onSuccess(Listener listener) {
		if (listener == null)
			doneListener = null;
		else
			doneListener = new WeakReference<Listener>(listener);
		return this;
	}

	public RequestObject<Listener> onFail(OnRequestFailListener listener) {
		if (listener == null)
			failListener = null;
		else
			failListener = new SoftReference<OnRequestFailListener>(listener);
		return this;
	}

	public RequestObject<Listener> onProgress(OnRequestProgressChangeListener listener) {
		if (listener == null)
			progressListener = null;
		else
			progressListener = new SoftReference<OnRequestProgressChangeListener>(listener);
		return this;
	}

	public RequestObject<Listener> holdListeners() {
		doneListenerHolder = doneListener.get();
		failListenerHolder = failListener.get();
		progressListenerHolder = progressListener.get();
		return this;
	}

	public void enque() {
	}

	public void onFail(String message) {
		if (globalOnFailListener != null)
			globalOnFailListener.onRequestFail(this, message);
		if (failListener != null) {
			OnRequestFailListener l = failListener.get();
			if (l != null)
				l.onRequestFail(this, message);
		}
	}

	public void onSuccess(String rawJson) {
		try {
			JSONObject json = new JSONObject(rawJson);
			int resCode = json.getInt("code");
			if (resCode != 1) {
				String msg = json.getString("msg");
				onFail(msg);
			}
			if(doneListener != null) {
				Listener l = doneListener.get();
				if(l != null) {
					Method m = listenerType.getDeclaredMethods()[0];
					Class<?>[] args = m.getParameterTypes();
					if (args.length > 0) {
						Gson gson = new Gson();
						Object result = gson.fromJson(json.getJSONObject("obj")
								.toString(), args[0]);
						m.invoke(l, result);
					} else {
						m.invoke(l);
					}
				}
			}
		} catch (Exception e) {
		}
	}
}

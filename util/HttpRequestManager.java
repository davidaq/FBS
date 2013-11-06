package util;

import java.io.IOException;
import java.io.UnsupportedEncodingException;
import java.util.Comparator;
import java.util.LinkedList;
import java.util.PriorityQueue;
import java.util.concurrent.Executor;
import java.util.concurrent.Executors;

import org.apache.http.NameValuePair;
import org.apache.http.client.ClientProtocolException;
import org.apache.http.client.HttpClient;
import org.apache.http.client.entity.UrlEncodedFormEntity;
import org.apache.http.client.methods.HttpPost;
import org.apache.http.client.methods.HttpUriRequest;
import org.apache.http.impl.client.DefaultHttpClient;

public final class HttpRequestManager implements Runnable {
	public final static HttpRequestManager manager = new HttpRequestManager();

	private HttpRequestManager() {

	}

	static class RequestComparator implements Comparator<RequestObject<?>> {
		public int compare(RequestObject<?> arg0, RequestObject<?> arg1) {

			return 0;
		}
	}

	private PriorityQueue<RequestObject<?>> queue = new PriorityQueue<RequestObject<?>>(
			10, new RequestComparator());

	private Executor exec = Executors.newFixedThreadPool(2);

	public void enque(RequestObject<?> request) {
		synchronized (queue) {
			queue.add(request);
		}
		exec.execute(this);
	}

	public void run() {
		RequestObject<?> requestObj = null;
		synchronized (queue) {
			if (!queue.isEmpty())
				requestObj = queue.remove();
		}
		if (requestObj == null)
			return;
		HttpClient client = new DefaultHttpClient();
		HttpPost request = new HttpPost(requestObj.getUrl());
		LinkedList<NameValuePair> nameValuePairs = new LinkedList<NameValuePair>();
		/*
		MultipartEntity entity = new MultipartEntity();
		entity.addPart("title",
				new StringBody("position.csv", Charset.forName("UTF-8")));
		File myFile = new File(Environment.getExternalStorageDirectory(), file);
		FileBody fileBody = new FileBody(myFile);
		entity.addPart("file", fileBody);
		httppost.setEntity(entity);
		httppost.getParams().setParameter("project", id);
		*/
		try {
			request.setEntity(new UrlEncodedFormEntity(nameValuePairs));
		} catch (UnsupportedEncodingException e1) {
			e1.printStackTrace();
		}
		try {
			client.execute(request);
		} catch (ClientProtocolException e) {
			e.printStackTrace();
		} catch (IOException e) {
			e.printStackTrace();
		}
	}

}

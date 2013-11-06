package util;

public interface OnRequestFailListener {
	public void onRequestFail(RequestObject<?> request, String message);
}

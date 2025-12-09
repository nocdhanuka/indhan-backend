package com.dhanuka.indhan.Fonts;

import android.content.Context;
import android.graphics.Typeface;
import android.util.AttributeSet;
import android.widget.TextView;

/**
 * Created By Wildnet Technologies Pvt. Ltd. on 8/26/2016.
 */

public class BoldTextView extends TextView {
    public BoldTextView(Context context) {
        super(context);
        if (!isInEditMode())
            init(context);
    }

    public BoldTextView(Context context, AttributeSet attrs) {
        super(context, attrs);
        if (!isInEditMode())
            init(context);
    }

    public BoldTextView(Context context, AttributeSet attrs, int defStyleAttr) {
        super(context, attrs, defStyleAttr);
        if (!isInEditMode())
            init(context);
    }

    public BoldTextView(Context context, AttributeSet attrs, int defStyleAttr, int defStyleRes) {
        super(context, attrs, defStyleAttr, defStyleRes);
        if (!isInEditMode())
            init(context);
    }

    private void init(Context context) {
        Typeface face = Typeface.createFromAsset(context.getAssets(), "fonts/Roboto-Bold.ttf");
        this.setTypeface(face);
    }
}

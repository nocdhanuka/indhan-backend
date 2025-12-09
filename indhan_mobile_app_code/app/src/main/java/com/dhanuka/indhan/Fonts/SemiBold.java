package com.dhanuka.indhan.Fonts;

import android.content.Context;
import android.graphics.Typeface;
import android.util.AttributeSet;
import android.widget.TextView;

/**
 * Created By Wildnet Technologies Pvt. Ltd. on 5/31/2016.
 */
public class SemiBold extends TextView {
    public SemiBold(Context context) {
        super(context);
        if (!isInEditMode())
            init(context);
    }

    public SemiBold(Context context, AttributeSet attrs) {
        super(context, attrs);
        if (!isInEditMode())
            init(context);
    }

    public SemiBold(Context context, AttributeSet attrs, int defStyleAttr) {
        super(context, attrs, defStyleAttr);
        if (!isInEditMode())
            init(context);
    }

    public SemiBold(Context context, AttributeSet attrs, int defStyleAttr, int defStyleRes) {
        super(context, attrs, defStyleAttr, defStyleRes);
        if (!isInEditMode())
            init(context);
    }

    private void init(Context context) {
        Typeface face = Typeface.createFromAsset(context.getAssets(), "fonts/Roboto-Black.ttf");
        this.setTypeface(face);
    }
}

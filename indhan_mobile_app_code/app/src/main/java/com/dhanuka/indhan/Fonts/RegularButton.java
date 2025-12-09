package com.dhanuka.indhan.Fonts;

import android.content.Context;
import android.graphics.Typeface;
import android.util.AttributeSet;
import android.widget.Button;

/**
 * Created By Wildnet Technologies Pvt. Ltd. on 6/6/2016.
 */
public class RegularButton extends Button {
    public RegularButton(Context context) {
        super(context);
        if (!isInEditMode())
            init(context);
    }

    public RegularButton(Context context, AttributeSet attrs) {
        super(context, attrs);
        if (!isInEditMode())
            init(context);
    }

    public RegularButton(Context context, AttributeSet attrs, int defStyleAttr) {
        super(context, attrs, defStyleAttr);
        if (!isInEditMode())
            init(context);
    }

    public RegularButton(Context context, AttributeSet attrs, int defStyleAttr, int defStyleRes) {
        super(context, attrs, defStyleAttr, defStyleRes);
        if (!isInEditMode())
            init(context);
    }

    private void init(Context context) {
        Typeface face = Typeface.createFromAsset(context.getAssets(), "fonts/Roboto-Regular.ttf");
        this.setTypeface(face);
    }
}

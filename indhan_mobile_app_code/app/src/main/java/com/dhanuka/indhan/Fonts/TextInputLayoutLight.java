package com.dhanuka.indhan.Fonts;

import android.content.Context;
import android.graphics.Typeface;
import com.google.android.material.textfield.TextInputLayout;
import android.util.AttributeSet;

/**
 * Created By Wildnet Technologies Pvt. Ltd. on 6/1/2016.
 */
public class TextInputLayoutLight extends TextInputLayout {
    public TextInputLayoutLight(Context context) {
        super(context);
        if (!isInEditMode())
            init(context);
    }

    public TextInputLayoutLight(Context context, AttributeSet attrs) {
        super(context, attrs);
        if (!isInEditMode())
            init(context);
    }

    public TextInputLayoutLight(Context context, AttributeSet attrs, int defStyleAttr) {
        super(context, attrs, defStyleAttr);
        if (!isInEditMode())
            init(context);
    }

    private void init(Context context) {
        Typeface face = Typeface.createFromAsset(context.getAssets(), "fonts/Roboto-Light.ttf");
        this.setTypeface(face);
    }
}

package com.dhanuka.indhan.util;

import android.text.InputFilter;
import android.text.SpannableString;
import android.text.Spanned;
import android.text.TextUtils;

/**
 * Created by Shivam on 3/17/2017.
 */

public class Util {

    public static InputFilter getEditTextFilterEmoji() {
        return new InputFilter() {
            @Override
            public CharSequence filter(CharSequence source, int start, int end, Spanned dest, int dstart, int dend) {
                CharSequence sourceOriginal = source;
                source = replaceEmoji(source);
                end = source.toString().length();
                if (end == 0)
                    return ""; //Return empty string if the input character is already removed
                if (!sourceOriginal.toString().equals(source.toString())) {
                    char[] v = new char[end - start];
                    TextUtils.getChars(source, start, end, v, 0);
                    String s = new String(v);
                    if (source instanceof Spanned) {
                        SpannableString sp = new SpannableString(s);
                        TextUtils.copySpansFrom((Spanned) source, start, end, null, sp, 0);
                        return sp;
                    } else {
                        return s;
                    }
                } else {
                    return null; // keep original
                }
            }

            private String replaceEmoji(CharSequence source) {

                String notAllowedCharactersRegex = "[^a-zA-Z0-9@#\\$%\\&\\-\\+\\(\\)\\*;:!\\?\\~`£\\{\\}\\[\\]=\\.,_/\\\\\\s'\\\"<>\\^\\|÷×]";
                return source.toString()
                        .replaceAll(notAllowedCharactersRegex, "");
            }

        };
    }
}

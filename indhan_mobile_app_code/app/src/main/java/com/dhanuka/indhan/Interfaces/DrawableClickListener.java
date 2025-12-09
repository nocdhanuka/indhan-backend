package com.dhanuka.indhan.Interfaces;

/**
 * Created by Shivam on 1/13/2017.
 */
public interface DrawableClickListener {
    public void onClick(DrawablePosition target);

    ;

    public static enum DrawablePosition {TOP, BOTTOM, LEFT, RIGHT}
}

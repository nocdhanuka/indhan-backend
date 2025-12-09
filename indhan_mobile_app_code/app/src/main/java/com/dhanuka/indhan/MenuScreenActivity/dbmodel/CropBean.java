package com.dhanuka.indhan.MenuScreenActivity.dbmodel;

import androidx.room.Entity;
import androidx.room.Index;
import androidx.room.PrimaryKey;

@Entity(indices = @Index(value = {"crop_id"}, unique = true))
public class CropBean {
    @PrimaryKey(autoGenerate = true)
    public int _id;
    public String crop;
    public String crop_id;
    public String product;
    public String product_id;
}

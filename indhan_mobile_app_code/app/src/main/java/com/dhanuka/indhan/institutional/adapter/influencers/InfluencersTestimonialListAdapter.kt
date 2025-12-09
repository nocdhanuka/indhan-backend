package com.dhanuka.indhan.institutional.adapter.influencers

import android.annotation.SuppressLint
import android.content.Context
import android.graphics.Bitmap
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.ImageView
import androidx.databinding.DataBindingUtil
import androidx.recyclerview.widget.RecyclerView
import com.bumptech.glide.Glide
import com.bumptech.glide.load.DataSource
import com.bumptech.glide.load.engine.GlideException
import com.bumptech.glide.request.RequestListener
import com.bumptech.glide.request.RequestOptions
import com.bumptech.glide.request.target.Target
import com.dhanuka.indhan.Constants.Constants.IMAGEBASEURL
import com.dhanuka.indhan.R
import com.dhanuka.indhan.databinding.ItemTestimonialBinding
import com.dhanuka.indhan.institutional.model.influencers.InfluencerTestimonialData

class InfluencersTestimonialListAdapter(
    var context: Context,
    var list: ArrayList<InfluencerTestimonialData>
) :
    RecyclerView.Adapter<InfluencersTestimonialListAdapter.ViewHolder>() {

    var layoutInflater = LayoutInflater.from(context)

    private lateinit var clickListener: OnClickItem

    fun setClickListener(itemClickListener: OnClickItem) {
        this.clickListener = itemClickListener
    }


    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ViewHolder {
        val binding: ItemTestimonialBinding =
            DataBindingUtil.inflate(layoutInflater, R.layout.item_testimonial, parent, false)
        return ViewHolder(binding)
    }

    override fun getItemCount(): Int {
        return list.size
    }

    override fun onBindViewHolder(holder: ViewHolder, position: Int) {
        holder.setData(position)
    }

    inner class ViewHolder(var view: ItemTestimonialBinding) : RecyclerView.ViewHolder(view.root) {
        // Holds the TextView that will add each animal to
        @SuppressLint("CheckResult")
        fun setData(position: Int) {
            view.txtName.text = "Name :- " + list[position].farmer
            view.txtProduct.text = list[position].product_id
            view.txtDescription.text = list[position].description
            view.txtTestimonialId.text = list[position].testimonial_id
            view.txtCreatedAt.text = list[position].created_at
            view.ratingBar.rating = list[position].rating.toFloat()

            val requestOptions = RequestOptions()
            requestOptions.placeholder(R.drawable.video_placeholder)
            requestOptions.centerInside()

            view.imgTestimonial.scaleType = ImageView.ScaleType.CENTER_INSIDE
            view.imgTestimonial.setImageResource(R.drawable.video_placeholder)

            if (list[position].uploaded_file != "") {
                view.loader.visibility = View.VISIBLE
                Glide.with(context)
                    .setDefaultRequestOptions(requestOptions)
                    .asBitmap()
                    .load(IMAGEBASEURL.plus(list[position].uploaded_file))
                    .listener(object : RequestListener<Bitmap> {
                        override fun onLoadFailed(
                            e: GlideException?,
                            model: Any?,
                            target: Target<Bitmap>?,
                            isFirstResource: Boolean
                        ): Boolean {
                            view.imgTestimonial.scaleType = ImageView.ScaleType.CENTER_INSIDE
                            view.imgTestimonial.setImageResource(R.drawable.video_placeholder)
                            view.loader.visibility = View.GONE
                            view.imgPlay.visibility = View.GONE
                            return false
                        }

                        override fun onResourceReady(
                            resource: Bitmap?,
                            model: Any?,
                            target: Target<Bitmap>?,
                            dataSource: DataSource?,
                            isFirstResource: Boolean
                        ): Boolean {
                            if (resource != null) {
                                view.imgTestimonial.scaleType = ImageView.ScaleType.CENTER_CROP
                                view.imgTestimonial.setImageBitmap(resource)
                            } else {
                                view.imgTestimonial.scaleType = ImageView.ScaleType.CENTER_INSIDE
                                view.imgTestimonial.setImageResource(R.drawable.video_placeholder)

                            }
                            view.loader.visibility = View.GONE
                            view.imgPlay.visibility = View.VISIBLE
                            return true
                        }
                    })
                    .into(view.imgTestimonial)
            } else {
                view.loader.visibility = View.GONE
            }

            view.imgTestimonial.setOnClickListener {
                clickListener.onClickView(it, list[absoluteAdapterPosition])
            }

            view.llayout.setOnClickListener {
                clickListener.onClickViewCircular(it, list[absoluteAdapterPosition])
            }
            view.layoutBtn.visibility = View.VISIBLE
        }

    }


    interface OnClickItem {
        fun onClickView(view: View, subMenu: InfluencerTestimonialData)
        fun onClickViewCircular(view: View, subMenu: InfluencerTestimonialData)
    }


}
package com.dhanuka.indhan.TestimonialList

import android.annotation.SuppressLint
import android.content.Context
import android.content.Intent
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
import com.dhanuka.indhan.R
import com.dhanuka.indhan.VideoPlayer.VideoPlayActivity
import com.dhanuka.indhan.databinding.ItemTestimonialBinding

class TestimonilListAdapter(var context: Context, var list: MutableList<TestimonialBean.Data>) :
    RecyclerView.Adapter<TestimonilListAdapter.ViewHolder>() {

    var layoutInflater = LayoutInflater.from(context)

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
            view.txtProduct.text = list[position].product
            view.txtDescription.text = list[position].description
            view.txtTestimonialId.text = list[position].uniqueTestimonialId
            view.txtCreatedAt.text = list[position].createdAt
            view.ratingBar.rating = list[position].rated.toFloat()

            val requestOptions = RequestOptions()
            requestOptions.placeholder(R.drawable.video_placeholder)
            requestOptions.centerInside()

            view.imgTestimonial.scaleType = ImageView.ScaleType.CENTER_INSIDE
            view.imgTestimonial.setImageResource(R.drawable.video_placeholder)

            if (list[position].uploadedFile != "") {
                view.loader.visibility = View.VISIBLE
                Glide.with(context)
                    .setDefaultRequestOptions(requestOptions)
                    .asBitmap()
                    .load(list[position].uploadedFile)
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
                val intent = Intent(context, VideoPlayActivity::class.java)
                intent.putExtra("VideoUrl", list[position].uploadedFile)
                context.startActivity(intent)
            }

        }

    }


}